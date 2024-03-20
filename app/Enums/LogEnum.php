<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 23:37
 */

namespace App\Enums;

class LogEnum
{
    const LOG_NAME = [
        'tieshuw.com' => [
            'error_log' => 'www.tieshuw.com.error.log',
            'access_log' => 'www.tieshuw.com.log'
        ],
        'tieshuw.la' => [
            'error_log' => 'www.tieshuw.la.error.log',
            'access_log' => 'www.tieshuw.la.log'
        ]
    ];

}
