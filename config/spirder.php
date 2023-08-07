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

        'domain' => 'https://www.mayiwxw.com',

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

        'article_url' => "https://www.mayiwxw.com/{--index--}_{--article_id--}/index.html"
    ],
];
