<?php

namespace App\Http\Controllers;

use App\Models\Chapter;
use App\Models\Domain;
use App\Models\ErrorChapter;
use App\Models\HandArticle;
use App\Services\ExcellentArticleService;
use App\Services\HttpProxyService;
use App\Services\SpiderService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use QL\QueryList;
use function Livewire\str;

class TestController extends Controller
{
    public function test()
    {
        Artisan::call("fix:chapter", [
            '--article_id' => 93685,
            '--site' => 'yingxiong',
        ]);
        echo 1;die;
        $key = '3d5007f6cc4763d314194b114f7d73a1';
        $domains = [
            "yuanwenba.com","5ccc.tw","soshukan.com","lewen55.net","zhanlianxue.net","qxs.la","l511.com","minishuwu.com","zwdu.app"
        ];

        $domains = array_chunk($domains, 3);
        foreach ($domains as $domain){
            $domain_urls = implode('|', $domain);
            $client = new Client([
                'base_uri' => "https://apistore.aizhan.com/site/hisinfos/{$key}?domains={$domain_urls}&type=0",
                'timeout' => 3.0
            ]);

            $response = $client->request('GET');
            $result = $response->getBody()->getContents();

//            $result = '{"code":200000,"status":"success","data":{"success":[{"domain":"hncsr.org","pc_br":0,"pc_sum":0,"pc_br_max":1,"pc_nums_max":11,"pc_max_time":"2017-10-27","pc_br_min":0,"pc_nums_min":2,"pc_min_time":"2017-08-17","m_br":0,"m_sum":0,"m_br_max":0,"m_nums_max":17,"m_max_time":"2022-11-26","m_br_min":0,"m_nums_min":1,"m_min_time":"2017-09-13"},{"domain":"fhzwxs.com","pc_br":0,"pc_sum":1,"pc_br_max":1,"pc_nums_max":50,"pc_max_time":"2022-12-15","pc_br_min":0,"pc_nums_min":15,"pc_min_time":"2022-11-02","m_br":0,"m_sum":0,"m_br_max":2,"m_nums_max":16,"m_max_time":"2022-11-08","m_br_min":0,"m_nums_min":9,"m_min_time":"2022-11-02"}],"count":2,"failed":[]},"msg":"\u8bf7\u6c42\u6210\u529f"}';
            $result = json_decode($result, true);

            if($result['code'] <> 200000){
                echo $domain_urls."\n";
                print_r($result);
                continue;
            }

            $result = $result['data']['success'];
            Domain::insert($result);
        }
        die;

        Artisan::call("collect:article", []);
        die;

        /** @var HttpProxyService $httpProxyService */
        $httpProxyService = app("HttpProxyService");

        for ($i = 7; $i < 488; $i++) {
            $proxy = $httpProxyService->proxy();
            $proxyAuth = base64_encode('DS2ZMP8Q' . ":" . '5F7CFBFE8427');
            $url = "https://top.aizhan.com/top/t3-15/p{$i}.html";

            try {
                $rt = QueryList::get($url, [], [
                    'proxy' => "http://" . $proxy,
                    'headers' => [
                        'User-Agent' => $httpProxyService->user_agent(),
                        'Accept-Encoding' => 'gzip, deflate, br',
                        "Proxy-Authorization" => "Basic " . $proxyAuth
                    ],
                    'timeout' => 30,
                ]);
                $rt = $rt->rules([
                    'name' => ['a', 'text'],
                    'domain' => ['em', 'text'],
                ])
                    ->range('.text>h2')->query()->getData();

                print_r($rt);
                die;
                foreach ($rt as $value) {
                    $info = [$value['name'] . '-' . $value['domain']];
                    logger('success', $info);
                }
                sleep(3);
                die;
            } catch (\Exception $exception) {
                sleep(1);
                print_r($exception->getMessage());
                logger('error', [$url]);
                die;
//                print_r($exception->getMessage());
            }
        }

        die;
        $client = new Client([
            'base_uri' => 'https://www.mayiwxw.com/115_115985/index.html',
            'timeout' => 3.0
        ]);

        $response = $client->request('GET', '', [
            'query' => [
                'time' => time(),
            ]
        ]);
        echo $response->getBody();
        die;
        $opts = [
            'http' => [
                'header' => 'Content-Type: text/html; charset=utf-8'
            ]
        ];
        $context = stream_context_create($opts);
        $content = file_get_contents('http://www.mayiwxw.com/115_115985/index.html', false, $context);
        echo $content;
        die;
        Artisan::call("fix:chapter", [
            '--article_id' => 1703,
            '--site' => 'xwbiquge',
        ]);
        die;
        $chapterNames = [
            '第176章 人道的诱惑，鸿钧不满',
            '第177章 诸圣出手，圣人级别大战',
            '第178章 钉死圣人，秦长生强势',
            '第179章 人道大兴，冥河屈服',
            '第180章 占据血海，后土机缘',
            '第181章 鸿钧谋划，盘古遗泽',
            '第182章 六道轮回，地道出世',
            '第183章 争夺地道，天道显化',
            '第184章 秦长生来历，六道轮回的归宿',
            '第185章 三分地道，汇聚紫霄宫',
            '第186章 鸿钧谋划，开辟地府',
            '第187章 魔祖出手，十日横空',
            '第188章 夸父死亡，后羿赶来',
            '第189章 后羿射日，大日陨落',
            '第190章 人族入劫，混乱征兆',
            '第191章 镇元子的目的',
            '第192章 红云机缘，开启拍卖会',
            '第193章 道祖吞噬过天道，天道的滋味真不错',
            '第194章 殿主不插手洪荒',
            '第195章 盘古心脏',
        ];

        foreach ($chapterNames as $chapterName) {
            $convertedChapterName = $this->convertChapterNameToChinese($chapterName);
            echo $convertedChapterName . "\n";
        }

    }

    function numberToChinese($number)
    {
        $chineseNumArr = array('零', '一', '二', '三', '四', '五', '六', '七', '八', '九');
        $chineseUnitArr = array('', '十', '百', '千', '万');
        $chineseNumber = '';
        $numStr = strval($number);
        $length = strlen($numStr);

        for ($i = 0; $i < $length; $i++) {
            $currentDigit = intval($numStr[$i]);

            if ($i == $length - 1 && $currentDigit == 1 && $length > 1) {
                $chineseNumber .= $chineseUnitArr[$i];
            } else {
                $chineseNumber .= $chineseNumArr[$currentDigit];
                if ($currentDigit != 0) {
                    $chineseNumber .= $chineseUnitArr[$length - $i - 1];
                }
            }
        }

        return $chineseNumber;
    }

    function convertChapterNameToChinese($chapterName)
    {
        $pattern = '/第(\d+)章/';
        preg_match_all($pattern, $chapterName, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $chapterNumber = intval($match);
                $chineseNumber = $this->numberToChinese($chapterNumber);
                $chapterName = str_replace($match, $chineseNumber, $chapterName);
            }
        }

        return $chapterName;

    }

}
