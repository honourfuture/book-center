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

class HttpProxyService
{
    public function proxy()
    {
        $proxy = $this->_api();

        return [
            'ip' => $proxy[0],
            'port' => $proxy[1],
            'protocol' => 'http'
        ];
    }

    public function proxy_url($proxy)
    {
        return "{$proxy['protocol']}://{$proxy['ip']}:{$proxy['port']}";
    }


    public function user_agent()
    {
        $user_agents =[
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Maxthon/4.9.5.1000 Chrome/39.0.2146.0 Safari/537.36",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:65.0) Gecko/20100101 Firefox/65.0",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0.3 Safari/605.1.15",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/18.17763",
            "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko",
        ];
        $rand = rand(0,5);
        return $user_agents[$rand];
    }

    private function _api()
    {
        $url = "http://proxy.siyetian.com/apis_get.html?token=gHbi1STqVkMOR1Zx8EVZBjT31STqFUeNpXQx0EVVFjT61EMPR0Y00ERjVTT6dGM.gMwMDN4ADN4YTM&limit=1&type=0&time=&split=1&split_text=&area=0&repeat=0&isp=0";

        $client = new Client([
            'headers' => ['User-Agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Maxthon/4.9.5.1000 Chrome/39.0.2146.0 Safari/537.36"]
        ]);

        $response = $client->get($url);

        $proxy = $response->getBody()->getContents();

        return explode(':', $proxy);
    }
}
