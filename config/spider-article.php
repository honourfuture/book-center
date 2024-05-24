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
        'domain' => 'http://101.200.132.193/tieshuw.php?url=https://www.mayiwsk.com',
        'pages' => [
            'home' => [
                'url' => 'http://101.200.132.193/tieshuw.php?url=https://www.mayiwsk.com',
                'rule' => [
                    'article_names' => ['.s2>a', 'texts'],
                    // DOM解析链接
                    'article_urls' => ['.s2>a', 'attrs(href)'],
                    'authors' => ['.s4', 'texts'],
                    'last_chapters' => ['.s3', 'texts'],
                ],

                'range' => '#newscontent .l',
                'add_rule' => [
                    'article_names' => ['.s2>a', 'texts'],
                    // DOM解析链接
                    'article_urls' => ['.s2>a', 'attrs(href)'],
                    'authors' => ['.s5', 'texts'],
                ],
                'add_range' => '#newscontent .r',
            ]
        ]
    ],
];
