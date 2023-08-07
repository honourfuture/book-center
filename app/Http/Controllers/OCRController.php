<?php

namespace App\Http\Controllers;


use AlibabaCloud\SDK\Ocrapi\V20210707\Ocrapi;
use AlibabaCloud\Darabonba\Stream\StreamUtil;
use AlibabaCloud\Tea\Model;
use \Exception;
use AlibabaCloud\Tea\Exception\TeaError;
use AlibabaCloud\Tea\Utils\Utils;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Ocrapi\V20210707\Models\RecognizeAdvancedRequest;
use AlibabaCloud\Tea\Utils\Utils\RuntimeOptions;
use Illuminate\Support\Facades\Storage;


class OCRController extends Controller
{
    public function do_ocr()
    {
        die;
        $chapter_ids = $this->_relation();
        $replace = [
            ' _《人在秦时，靠刷取词条改变世界》小说在线阅读 - 起点中文网 - www.qidian.com',
            'RZQS-KSQCTGBSJ/'
        ];

        $storage = Storage::disk('ocr');
        $storage_txt = Storage::disk('article');
        $images = $storage->allFiles("/RZQS-KSQCTGBSJ");

        $origin_chapters = [];
        foreach ($images as $image) {
            $article_name = str_replace($replace = [
                ' _《人在秦时，靠刷取词条改变世界》小说在线阅读 - 起点中文网 - www.qidian.com',
                'RZQS-KSQCTGBSJ/',
                ' _《人在秦时，靠刷取词条改变世界》小说在线阅读 - 起点中文网\' - www.qidian.com',
                '.png'
            ], '', $image);

            $path = $storage->path($image);
            $id = array_search($article_name, $chapter_ids);
            $origin_chapters[$article_name]['path'] = $path;
            $origin_chapters[$article_name]['chapter_id'] = $id;
            $origin_chapters[$article_name]['local_txt'] = "/20/{$id}.txt";
        }
        print_r($origin_chapters);die;

        $client = self::createClient(config('aliyun-ocr.access_key_id'), config('aliyun-ocr.access_key_secret'));

        foreach ($origin_chapters as $article_name => $chapter) {
            $file = $chapter['path'];
            // 需要安装额外的依赖库，直接点击下载完整工程即可看到所有依赖。
            $bodyStream = StreamUtil::readFromFilePath($file);
            $recognizeAdvancedRequest = new RecognizeAdvancedRequest([
                "body" => $bodyStream,
                "needSortPage" => true,
                "paragraph" => true,
                "row" => false
            ]);
            $runtime = new RuntimeOptions([]);
            try {
                // 复制代码运行请自行打印 API 的返回值
                $result = $client->recognizeAdvancedWithOptions($recognizeAdvancedRequest, $runtime);
                $result = json_decode($result->body->data, true);
                $text = $article_name . "\r\n";

                foreach ($result['prism_paragraphsInfo'] as $prism_paragraphs) {
                    $text .= $prism_paragraphs['word'] . "\n";
                }

                $text = iconv('utf-8', 'gbk//IGNORE', $text);
                $storage_txt->put($chapter['local_txt'], $text);

            } catch (Exception $error) {
                if (!($error instanceof TeaError)) {
                    $error = new TeaError([], $error->getMessage(), $error->getCode(), $error);
                }
                // 如有需要，请打印 error
                echo Utils::assertAsString($error->message);
            }

        }


    }

    /**
     * 使用AK&SK初始化账号Client
     * @param string $accessKeyId
     * @param string $accessKeySecret
     * @return Ocrapi Client
     */
    public static function createClient($accessKeyId, $accessKeySecret)
    {
        $config = new Config([
            // 必填，您的 AccessKey ID
            "accessKeyId" => $accessKeyId,
            // 必填，您的 AccessKey Secret
            "accessKeySecret" => $accessKeySecret
        ]);
        // Endpoint 请参考 https://api.aliyun.com/product/ocr-api
        $config->endpoint = "ocr-api.cn-hangzhou.aliyuncs.com";
        return new Ocrapi($config);
    }

    private function _relation()
    {
        return [
            '10338104' => '第81章 有用，但不多（求首订）',
            '10338105' => '第82章 什么皇帝的新丹（求首订）',
            '10338106' => '第83章 偷药贼和灵药守护者（求首订）',
            '10338107' => '第84章 我是来谈合作的（求首订）',
            '10338108' => '第85章 无师自通（求首订）',
            '10338109' => '第86章 切磋？是事故！（求订阅）',
            '10338110' => '第87章 不想当训练家的我，灵宠太努力了（求订阅）',
            '10338111' => '第88章 我要在此澄清，那不是谣言（求订阅）',
            '10338112' => '第89章 秀色可餐，不外如是（求订阅）',
            '10338113' => '第90章 今晚是想选猫，还是想选我？（求订阅）',
            '10338114' => '第91章 少年强，则少女扶墙（求订阅）',
            '10338115' => '第92章 你再敢用心若止水，那就不要碰我！（求订阅）',
            '10338117' => '第93章 即便是村子里的狗也能和他过两招',
            '10338118' => '第94章 要不什么时候去打劫一下罗网',
            '10338119' => '第95章 智慧果没法让人增长智慧',
            '10338120' => '第96章 来时孑然一身，回时一大家子',
            '10338121' => '第97章 狐假虎威的感觉还不错',
            '10338122' => '第98章 论白渊的桃花运是否和桃花剑有关',
            '10357010' => '第99章 我看今晚就该把你给他送过去',
            '10357013' => '第100章 bug针法，集杀与医二意',
            '10432448' => '第101章 把她定住，然后就能为所欲为',
            '10432454' => '第102章 女妖精应该不算是女人吧？',
            '10508955' => '第103章 就是北冥子见到我都得恭恭敬敬的',
            '10508956' => '第104章 师弟啊，我恭喜你发财了！',
            '10544025' => '第105章 人不该，但是可以试试',
            '10544028' => '第106章 既然要追求刺激，那就贯彻到底喽',
            '10595357' => '第107章 直接当着她的面不是更刺激么',
            '10595363' => '第108章 天道监考官，秦时的外语考试',
            '10648367' => '第109章 求你别说了！我信了还不行么',
            '10648373' => '第110章 要把太乙山的飞禽都警告一遍',
            '10677742' => '第111章 肉包子打狗，有去无回',
            '10677744' => '第112章 完整地【即墨剑式】',
            '10733476' => '第113章 到时候我连你一起吃了',
            '10733483' => '第114章 你这可是在玩火',
            '10733487' => '第115章 道家的名剑收藏',
            '10733493' => '第116章 双金色词条，闪瞎双眼',
            '10733499' => '第117章 我不装了，我摊牌了',
            '10733505' => '第118章 五年之约',
            '10733517' => '第119章 飞剑？不，是玩具飞剑',
            '10733523' => '第120章 又快又短！',
            '12708130' => '第121章 我师弟白渊有顶级铸剑师之资',
            '12708139' => '第122章 两份拜山帖',
            '12708145' => '第123章 那双眼睛，有秘密',
            '12708152' => '第124章 怎么阴阳家的人都喜欢夜袭呢？',
            '12708159' => '第125章 越是漂亮的女人，就越会骗人',
            '12708165' => '第126章 以后，我就是太乙山的猫大王了',
            '12708171' => '第127章 小猫咪会有什么坏心思呢？',
            '12708175' => '第128章 三大长老有五位不是常识么',
            '12708190' => '第129章 你甚至都无法让我感到一丝疼痛',
            '12708195' => '第130章 以魂换气，以气化力',
            '12708200' => '第131章 人不如猫',
            '12708207' => '第132章 这是什么鬼花，为何长着狗头？',
            '12708214' => '第133章 人力岂是如此不便之物',
            '12708218' => '第134章 一剑超人',
            '12708224' => '第135章 后备隐藏能源',
            '12708233' => '第136章 让这天地欢呼，此剑诞生',
            '12891927' => '第137章 我要是白渊，我比他还大方',
            '12891936' => '第138章 少女永远18岁',
            '13043008' => '第139章 绝对不做软饭王',
            '13043012' => '第140章 金色桃花剑',
            '13194914' => '第141章 这树怎么叶子都不长？',
            '13194917' => '第142章 五光十色太乙山',
            '13194919' => '第143章 一朝顿悟，剑仙指日可待',
            '13321739' => '第144章 今晚我要在这睡',
            '13635716' => '第145章 具体细节呢？',
            '15121207' => '第146章 纸张问世，时代变革的中心',
            '15121218' => '第147章 儒道联盟',
            '15121226' => '第148章 坏了！我师弟留不住了！',
            '15121230' => '第149章 被追杀的宿命',
            '15121241' => '第150章 生活嘛，就该精致一点',
            '15121248' => '第151章 救命稻草',
            '15121251' => '第152章 治病居然找到罪魁祸首这来了',
            '15121257' => '第153章 人都走了，还得给他擦屁股',
            '15121265' => '第154章 超级赛亚人？超级赛亚猫',
            '15121270' => '第155章 千方百计只为能够接近他',
            '15121276' => '第156章 你的心不会痛么？',
            '15121282' => '第157章 猫猫的初次变身',
            '15121288' => '第158章 这么危险的事情，还是离得远一些比较好。',
            '15121296' => '第159章 风胡子后人',
            '15121302' => '第160章 我们不需要剑谱排名',
            '15121308' => '第161章 又见世界词条',
            '15121315' => '第162章 世界，拒绝了我',
            '15421728' => '第163章 我就没见过这么抠搜的馈赠',
            '15421732' => '第164章 靠水吃水',
            '15569173' => '第165章 吃完之后，感觉自己飘了',
            '16069888' => '第166章 救命之恩，无以为报',
            '16069892' => '第167章 十足的颜控',
            '16069896' => '第168章 入HD而知繁华',
            '16069899' => '第169章 我也想看你们跳舞',
            '16069902' => '第170章 这是来砸场子的？',
            '16069906' => '第171章 年龄不是问题',
            '16069909' => '第172章 这客栈我是一刻都待不下去了',
            '16069913' => '第173章 不要钱',
            '16199432' => '第174章 一曲《高山》见不周',
            '16199434' => '第175章 雪儿：有脏东西',
            '17076905' => '第176章 小样儿，叫你吓我！',
            '17076907' => '第177章 学舞就来妃雪阁',
            '17076909' => '第178章 天然的好感',
            '17076911' => '第179章 我白某人又白嫖了？',
            '17076914' => '第180章 这饭，真是一步到胃了',
            '17076916' => '第181章 既然来了，那就别走了',
            '17076919' => '第182章 麻烦还是一次解决得好',
            '17076920' => '第183章 红颜祸水啊',
            '17076922' => '第184章 我是正经人，对妃雪阁没兴趣',
            '17076923' => '第185章 这些事情你们听听就好，千万别传出去',
            '17301422' => '第186章 强行送一波礼物',
            '17301426' => '第187章 不好意思，她们是我的人了',
        ];
    }


}

