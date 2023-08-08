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
        $chapter_ids = $this->_relation();
        $replace = [
            ' _《从皇马踢后腰开始》小说在线阅读 - 起点中文网 - www.qidian.com',
            'RZQS-KSQCTGBSJ/'
        ];

        $storage = Storage::disk('ocr');
        $storage_txt = Storage::disk('article');
        $images = $storage->allFiles("/CHMTHYKS");

        $origin_chapters = [];
        foreach ($images as $image) {
            $article_name = str_replace([
                ' _《从皇马踢后腰开始》小说在线阅读 - 起点中文网 - www.qidian.com',
                'CHMTHYKS/',
                '  _《从皇马踢后腰开始》小说在线阅读 - 起点中文网 - www.qidian.com',
                '.png'
            ], '', $image);

            $path = $storage->path($image);
            $id = array_search($article_name, $chapter_ids);
            $origin_chapters[$article_name]['path'] = $path;
            $origin_chapters[$article_name]['chapter_id'] = $id;
            $origin_chapters[$article_name]['local_txt'] = "/143/{$id}.txt";
        }

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
            '18326023' => '第一百八十三章 罗贝里+穆勒+高中锋，这就是巅峰拜仁【求大佬们订阅！】',
            '18348660' => '第一百八十四章 我们即是传奇，穆式皇马万岁！【求大佬们订阅！】',
            '18477802' => '第一百八十五章 我不要钱，先生，我要当老大【求大佬们订阅！】',
            '18560815' => '第一百八十六章 进攻天赋大提升，豪强馋李昂馋得流口水【求订阅！】',
            '18721469' => '第一百八十七章 穆式皇马终结局，六大豪强的李昂争夺战【求订阅！】',
            '18721470' => '第一百八十八章 我们切尔西现在可以说是伦敦的老大了！【求订阅！】',
            '18721471' => '第一百八十九章 潜力不小，问题不少，但穆里尼奥有把握掀起青春风暴！',
            '18721472' => '第一百九十章 李昂的兄弟电话，兹拉坦降临蓝桥！【求订阅！】',
            '18721474' => '第一百九十一章 体系？李昂自己就是一个攻防体系【求大佬们订阅！】',
            '18733149' => '第一百九十二章 冷静小狮子和野兽兹拉坦 【求大佬们订阅！】',
            '18799639' => '第一百九十三章 瓜穆相看重现英超！马蒂奇的夏窗收官交易【求订阅！】',
            '18805132' => '第一百九十四章 开场就造球，小狮子你玩真的啊？【求大佬们订阅！】',
            '18867650' => '第一百九十五章 从神鬼莫测到替补奇兵？看我比利时小爆趟！',
            '18883703' => '第一百九十六章 两场造三球，强劲核心持续发力，曼联要遭重【求订阅！】',
        ];
    }


}

