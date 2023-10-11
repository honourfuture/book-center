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
use Jaeger\Cache;

class HttpProxyService
{
    public function proxy()
    {
        return $this->_api();

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
        $user_agents = [
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Maxthon/4.9.5.1000 Chrome/39.0.2146.0 Safari/537.36",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.75 Safari/537.36",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:65.0) Gecko/20100101 Firefox/65.0",
            "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/12.0.3 Safari/605.1.15",
            "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.140 Safari/537.36 Edge/18.17763",
            "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; rv:11.0) like Gecko",
        ];
        $rand = rand(0, 5);
        return $user_agents[$rand];
    }

    public function _api()
    {
        return $this->qg();
        return $this->_hide_my_name();
    }

    //https://hidemyna.me/en/proxy-list/?type=h#list
    private function _hide_my_name()
    {
        $proxies = [
            '193.151.180.215:42088',
//            '173.46.82.194:54152',
            '185.238.214.96:48641',
        ];

        $rand = rand(0,1);

        return explode(':', $proxies[$rand]);
    }

    private function qg(){
        $cacheKey = 'qg_key';


        $server = \Illuminate\Support\Facades\Cache::get($cacheKey);
        // 如果缓存存在，则返回缓存中的server值，否则发送请求获取server值
        if ($server) {
            return $server;
        } else {
            // 发送请求
            $client = new Client();
            $response = $client->get('https://share.proxy.qg.net/get?key=DS2ZMP8Q&num=1&area=&isp=&format=json&seq=&distinct=false&pool=1');
            // 解析返回的JSON数据
            $jsonData = $response->getBody();
            $data = json_decode($jsonData, true);

            // 提取server的值
            if ($data && isset($data['data'][0]['server'])) {
                $server = $data['data'][0]['server'];

                // 设置一分钟缓存
                $minutes = 1;
                \Illuminate\Support\Facades\Cache::put($cacheKey, $server, $minutes);
            }

            return $server;
        }
    }

}
