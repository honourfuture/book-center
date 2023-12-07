<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/8/6
 * Time: 11:43
 */
return [
    'mayi' => [
        'name' => 'mayi',
        'charset' => 'utf-8',
        'domain' => 'https://www.mayiwxw.com',
        'url' => 'https://www.mayiwxw.com',
        'get_article_info_rule' => [
            'book_name' => ['meta:eq(11)', 'content'],
            'author' => ['meta:eq(10)', 'content'],
            'desc' => ['#intro', 'text']
        ],

        'get_article_rule' => [
            'chapters' => ['a', 'texts'],
            // DOM解析链接
            'chapter_hrefs' => ['a', 'attrs(href)'],
        ],

        'get_article_range' => '#list>dl',

        'get_chapter_find' => '#content',

        'article_url' => "https://www.mayiwxw.com/{--index--}_{--article_id--}/index.html",

        'content_preg' => function ($text) {
            $text = str_replace([
                '最新网址：www.mayiwxw.com',
                '蚂蚁文学',
                'www.mayiwxw.com',
                '全文字更新,牢记网址:',
                '<div id="center_tip"><b></b></div>',
                '<p>',
                '<b>',
                '</b>'
            ], '', $text);

            $text = str_replace([
                '</p>',
                '<br/>',
                '<br />',
                '<br>'
            ], '', $text);

            return $text;
        }
    ],

    'tt' => [
        'name' => 'tt',
        'charset' => 'utf-8',
        'domain' => 'https://www.ttshuba.org',
        'url' => 'https://www.ttshuba.org',
        'get_article_info_rule' => [
            'book_name' => ['meta:eq(4)', 'content'],
            'author' => ['meta:eq(8)', 'content'],
            'desc' => ['.introtxt', 'text']
        ],

        'get_article_rule' => [
            'chapters' => ['a', 'texts'],
            // DOM解析链接
            'chapter_hrefs' => ['a', 'attrs(href)'],
        ],

        'get_article_range' => '#list>dl',

        'get_chapter_find' => '#content',

        'article_url' => "https://www.ttshuba.org/shu/{--article_id--}",

        'content_preg' => function ($text) {
            $text = str_replace([
                '网页版章节内容慢，请下载好阅小说app阅读最新内容</p>',
                '网页版章节内容慢，请下载好阅小说app阅读最新内容',
                '请退出转码页面，请下载好阅小说app阅读最新章节。</p>',
                '请退出转码页面，请下载好阅小说app阅读最新章节。',
                '想要看最新章节内容，请下载好阅小说app，无广告免费阅读最新章节内容。</p>',
                '想要看最新章节内容，请下载好阅小说app，无广告免费阅读最新章节内容。',
                '网站已经不更新最新章节内容，最新章节内容已经在好阅小说app更新。</p>',
                '网站已经不更新最新章节内容，最新章节内容已经在好阅小说app更新。',
                '<p>',
                '<b>',
                '</b>'
            ], '', $text);

            $str = preg_replace('/<div class="bottem">.*$/si', '', $text);
            if ($str) {
                $text = $str;
            }

            $text = str_replace([
                '</p>',
            ], "\r\n", $text);

            $text = str_replace([
                '</p>',
                '<br/>',
                '<br />',
                '<br>'
            ], '', $text);

            $text = preg_replace('/(<div\b[^>]*>|<\/div>|<h1\b[^>]*>[^<]*<\/h1>|<script\b[^>]*>[^<]*<\/script>|<span\b[^>]*>[^<]*<\/span>|<a\b[^>]*>[^<]*<\/a>)/i', " ", $text);

            return $text;
        }
    ],

    '69shu' => [
        'name' => '69shu',
        'charset' => 'gbk',
        'domain' => 'http://154.9.27.55/q.php?url=https://www.69shuba.com',
        'url' => 'http://154.9.27.55/q.php?url=',
        'get_article_info_rule' => [
            'book_name' => ['meta:eq(11)', 'content'],
            'author' => ['meta:eq(10)', 'content'],
            'desc' => ['#intro', 'text']
        ],

        'get_article_rule' => [
            'chapters' => ['a', 'texts'],
            // DOM解析链接
            'chapter_hrefs' => ['a', 'attrs(href)'],
        ],

        'get_article_range' => '#catalog',

        'get_chapter_find' => '.txtnav:text',

        'article_url' => "http://154.9.27.55/q.php?url=https://www.69shuba.com/{--article_id--}/",

        'content_preg' => function ($text) {
            $text = str_replace([
                '<p>',
                '</p>',
                'M。97',
                'Xiaoshuo'
            ], '', $text);

            $text = preg_replace('/(<div\b[^>]*>|<\/div>|<h1\b[^>]*>[^<]*<\/h1>|<script\b[^>]*>[^<]*<\/script>|<span\b[^>]*>[^<]*<\/span>|<a\b[^>]*>[^<]*<\/a>)/i', '', $text);
            //删除多余的空行
            $text = preg_replace('/^\h*\v+/m', '', $text);
            $text = htmlentities($text);

            $text = str_replace('&lt;br&gt;', "", $text);
            $text = str_replace('&emsp;&emsp;', '', $text);
            $text = str_replace('                ', '', $text);
            $text = html_entity_decode($text);
            return $text;
        }
    ],

    'xwbiquge' => [
        'name' => 'xwbiquge',
        'charset' => 'utf-8',
        'domain' => 'http://101.42.27.27/tieshuw.php?url=http://www.xwbiquge.com',
        'url' => 'http://101.42.27.27/tieshuw.php?url=http://www.xwbiquge.com',
        'get_article_info_rule' => [
            'book_name' => ['meta:eq(14)', 'content'],
            'author' => ['meta:eq(12)', 'content'],
            'desc' => ['meta:eq(6)', 'content']
        ],

        'get_article_rule' => [
            'chapters' => ['a', 'texts'],
            // DOM解析链接
            'chapter_hrefs' => ['a', 'attrs(href)'],
        ],

        'get_article_range' => '#list>dl',

        'get_chapter_find' => '#booktxt:text',

        'article_url' => "http://101.42.27.27/tieshuw.php?url=http://www.xwbiquge.com/biquge_{--article_id--}/",

        'content_preg' => function ($text) {
            $text = str_replace([
                '<p>',
                '</p>',
            ], '', $text);

            $text = preg_replace('/(<div\b[^>]*>|<\/div>|<h1\b[^>]*>[^<]*<\/h1>|<script\b[^>]*>[^<]*<\/script>|<span\b[^>]*>[^<]*<\/span>|<a\b[^>]*>[^<]*<\/a>)/i', '', $text);
            //删除多余的空行
            $text = preg_replace('/^\h*\v+/m', '', $text);
            $text = htmlentities($text);

            $text = str_replace('&lt;br&gt;', "", $text);
            $text = str_replace('&emsp;&emsp;', '', $text);
            $text = str_replace('                ', '', $text);
            $text = html_entity_decode($text);
            return $text;
        },
        'next_page' => [
            'rule' => [
                'url' => ['#next_url:eq(0)', 'attrs(href)'],
                'text' => ['#next_url:eq(0)', 'texts'],
            ],
            'has_text' => '下一页',
            'range' => '.bottem1'
        ],
    ],

    '00shu' => [
        'name' => '00shu',
        'charset' => 'utf-8',
        'domain' => 'http://www.00kanshu.cc',
        'url' => 'http://www.00kanshu.cc',
        'get_article_info_rule' => [
            'book_name' => ['meta:eq(14)', 'content'],
            'author' => ['meta:eq(12)', 'content'],
            'desc' => ['meta:eq(6)', 'content']
        ],

        'get_article_rule' => [
            'chapters' => ['a', 'texts'],
            // DOM解析链接
            'chapter_hrefs' => ['a', 'attrs(href)'],
        ],

        'get_article_range' => '#list>dl',

        'get_chapter_find' => '#booktxt:text',

        'article_url' => "http://www.00kanshu.cc/book/{--article_id--}/",

        'content_preg' => function ($text) {
            $text = str_replace([
                '<p>',
                '</p>',
            ], '', $text);

            $text = preg_replace('/(<div\b[^>]*>|<\/div>|<h1\b[^>]*>[^<]*<\/h1>|<script\b[^>]*>[^<]*<\/script>|<span\b[^>]*>[^<]*<\/span>|<a\b[^>]*>[^<]*<\/a>)/i', '', $text);
            //删除多余的空行
            $text = preg_replace('/^\h*\v+/m', '', $text);
            $text = htmlentities($text);

            $text = str_replace('&lt;br&gt;', "", $text);
            $text = str_replace('&emsp;&emsp;', '', $text);
            $text = str_replace('                ', '', $text);
            $text = html_entity_decode($text);
            return $text;
        },
        'next_page' => [
            'rule' => [
                'url' => ['#next_url:eq(0)', 'attrs(href)'],
                'text' => ['#next_url:eq(0)', 'texts'],
            ],
            'has_text' => '下一页',
            'range' => '.bottem1'
        ],
    ],
];
