<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 17:43
 */

namespace App\Services;


use GuzzleHttp\Client;
use QL\QueryList;

class SpiderService
{
    public function get_article($url, $config = [])
    {
        // 元数据DOM解析规则
        $rules = [
            // DOM解析文章标题
            'chapters' => ['a', 'texts'],
            // DOM解析链接
            'chapter_hrefs' => ['a', 'attrs(href)'],
        ];

        $range = '#list>dl';
        $rt = QueryList::get($url)->rules($rules)
            ->range($range)->query()->getData();

        return $rt->all();
    }

    public function get_chapter($url, $config = [])
    {
//        $url = 'https://www.mayiwxw.com/99_99388/44558017.html';
//        $url = 'https://www.mayiwxw.com/99_99388/47018138.html';

        /** @var HttpProxyService $httpProxyService */
        $httpProxyService = app('HttpProxyService');
        $user_agent = $httpProxyService->user_agent();

        $text = QueryList::get($url, [], [
            'headers' => [
                'User-Agent' => $user_agent,
                'Accept-Encoding' => 'gzip, deflate, br',
            ]
        ])->find('#content')->text();

        sleep(3);

        $text = str_replace('最新网址：www.mayiwxw.com', '', $text);
        $text = str_replace('蚂蚁文学', '铁书网', $text);
        $text = str_replace('www.mayiwxw.com ', 'www.tieshw.com', $text);

        return $text;
    }


}
