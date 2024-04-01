<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 23:37
 */

namespace App\Enums;

class LogEnum extends BaseEnum
{
    const LOG_PATH = '/www/wwwlogs/';
    const LOG_NAME = [
        'tieshuw.com' => [
            'error_log' => 'www.tieshuw.com.error.log',
            'access_log' => 'www.tieshuw.com.log'
        ],
        'tieshuw.la' => [
            'error_log' => 'tieshuw.la.error.log',
            'access_log' => 'tieshuw.la.log'
        ],
        'tianz.la.log' => [
            'error_log' => 'tianz.la.error.log',
            'access_log' => 'tianz.la.log',
        ],
        'tianz.net.log' => [
            'error_log' => 'tianz.net.error.log',
            'access_log' => 'tianz.net.log',
        ],
        'qianbidushu.com' => [
            'error_log' => 'qianbidushu.com.error.log',
            'access_log' => 'qianbidushu.com.log',
        ],
        'm.qianbidushu.com' => [
            'error_log' => 'm.qianbidushu.com.log',
            'access_log' => 'm.qianbidushu.com.log',
        ],
        'lexinren.top' => [
            'error_log' => 'lexinren.top.error.log',
            'access_log' => 'lexinren.top.log',
        ],
        '77xs8.net' => [
            'error_log' => '77xs8.net.error.log',
            'access_log' => '77xs8.net.log',
        ],
        '35wxw.com' => [
            'error_log' => '35wxw.com.error.log',
            'access_log' => '35wxw.com.log',
        ],
        'www.aiks98.com' => [
            'error_log' => 'www.aiks98.com.error.log',
            'access_log' => 'www.aiks98.com.log',
        ],
        'www.paoshuba.cc' => [
            'error_log' => 'www.paoshuba.cc.error.log',
            'access_log' => 'www.paoshuba.cc.log',
        ],
    ];

    const LOG_SHELL = [
        'spider' => 'cat {file_path} | grep {spider} | grep {month_day} | wc -l',
        'top' => " tail -2000 {file_path} |awk '{print $1}' |sort |uniq -c |sort -nr |head -100"
    ];

}
