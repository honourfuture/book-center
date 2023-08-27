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

class BaiduTjService
{
    private $token_param;

    private $api_url;

    public function __construct()
    {
        $this->token_param = [
            'access_token' => config('baidu-tj.access_token'),
            'site_id' => config('baidu-tj.site_id'),
        ];

        $this->api_url = config('baidu-tj.base_uri');
    }
    public function toppage($start_date, $end_date)
    {
        $method = 'visit/toppage/a';

        $metrics = [
            'pv_count',
            'visitor_count',
            'ip_count',
        ];

        $query = array_merge($this->token_param, [
            'method' => $method,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'metrics' => implode(',', $metrics),
            'max_results' => 50
        ]);

        $queryString  = http_build_query($query);
        $url = $this->api_url. '?' . $queryString;

        $result = $this->_get_data($url);

        print_r($result);die;
        echo $url;die;
    }

    private function _get_data($url)
    {
        $client = new Client([
            'base_uri' => $url,
            'timeout' => 3.0
        ]);

        $response = $client->request('GET');

        return $response->getBody()->getContents();
    }

}
