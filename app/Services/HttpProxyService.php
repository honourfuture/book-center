<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 17:43
 */

namespace App\Services;


class HttpProxyService
{
    public function proxy()
    {
        return [
            'ip' => '47.92.113.71',
            'port' => '80',
            'protocol' => 'http'
        ];
    }

    public function proxy_url($proxy)
    {
        return "{$proxy['protocol']}://{$proxy['ip']}:{$proxy['port']}";
    }


    public function user_agent()
    {
        return "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Maxthon/4.9.5.1000 Chrome/39.0.2146.0 Safari/537.36";
    }
}
