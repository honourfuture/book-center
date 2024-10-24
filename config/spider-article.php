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
    'biquduwx' => [
        'name' => 'biquduwx',
        'charset' => 'utf-8',
        'domain' => 'http://58.87.95.199/bssw_com.php?url=http://wap.biquduwx.com/top/lastupdate_1/',
        'pages' => [
            'home' => [
                'url' => 'http://58.87.95.199/bssw_com.php?url=http://wap.biquduwx.com/top/lastupdate_1/',
                'rule' => [
                    'article_names' => ['.p2>a', 'text'],
                    // DOM解析链接
                    'article_urls' => ['.p2>a', 'attr(href)'],
                    'authors' => ['.p3', 'text'],
                    'last_chapters' => ['.s3', 'text'],
                ],

                'range' => '.user_content .content_link',
                'add_rule' => [],
                'add_range' => '',
            ]
        ]
    ],
    'yingxiong' => [
        'name' => 'yingxiong',
        'charset' => 'gbk',
        'domain' => 'http://www.jiuhongbao.com/a1a0html/q.php?url=https://www.yingxiongxs.com',
        'pages' => [
            'home' => [
                'url' => 'http://101.200.132.193/tieshuw.php?url=https://www.yingxiongxs.com',
                'rule' => [
                    'article_names' => ['.name', 'texts'],
                    // DOM解析链接
                    'article_urls' => ['.section', 'attrs(href)'],
                    'authors' => ['.author', 'texts'],
                    'last_chapters' => ['.section', 'texts'],
                ],

                'range' => '.update-table',
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
