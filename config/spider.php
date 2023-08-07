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
            return str_replace([
                '最新网址：www.mayiwxw.com',
                '蚂蚁文学',
                'www.mayiwxw.com',
                '<div id="center_tip"><b></b></div>',
            ], '', $text);
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
            return $text;
        }
    ],

    '69shu' => [
        'name' => '69shu',
        'charset' => 'gbk',
        'domain' => 'https://www.69shu.com',
        'url' => '',
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

        'article_url' => "https://www.69shu.com/{--article_id--}/",

        'content_preg' => function ($text) {
            $text = preg_replace('/(<div\b[^>]*>|<\/div>|<h1\b[^>]*>[^<]*<\/h1>|<script\b[^>]*>[^<]*<\/script>|<span\b[^>]*>[^<]*<\/span>)/i', '', $text);
            $text = preg_replace('/^\h*\v+/m', '', $text);
            $text = str_replace('                  ', '', $text);
            return $text;
        }
    ],
];
