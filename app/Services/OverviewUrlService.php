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

class OverviewUrlService
{
    public function overview_urls($source, $start_date, $end_date)
    {
        $urls = [];
        switch ($source) {
            case 'baidu':
                list($start_date, $end_date) = $this->_format_date('Ymd', $start_date, $end_date);
                $urls = $this->_baidu_top($start_date, $end_date);
                break;
            case 'nginx':
                $a = app('es')->info();
                print_r($a);
                $urls = [];
                break;
        }
        $article_ids = [];
        foreach ($urls as $url){
            $url = $this->_match_article_id($url);
            if($url){
                $article_ids[] = $url;
            }
        }

        return $article_ids;
    }

    /**
     * @param $format
     * @param $start_date
     * @param $end_date
     * @return array
     */
    private function _format_date($format, $start_date, $end_date): array
    {
        $start_time = strtotime($start_date);
        $end_time = strtotime($end_date);

        return [
            date($format, $start_time), date($format, $end_time)
        ];
    }

    private function _baidu_top($start_date, $end_date)
    {
        $urls = [];
        $access_token = config('baidu-analyse.access_token');
        $site_id = config('baidu-analyse.site_id');
        $base_uri = config('baidu-analyse.base_uri');

        $query = [
            'site_id' => $site_id,
            'access_token' => $access_token,
            'method' => 'visit/toppage/a',
            'start_date' => $start_date,
            'end_date' => $end_date,
            'metrics' => 'pv_count',
            'max_results' => 3000
        ];

        $client = new Client([
            'base_uri' => $base_uri,
            'timeout' => 50.0
        ]);

        $response = $client->request('GET', $base_uri, [
            'query' => $query
        ]);

        $response = $response->getBody()->getContents();
        $response = json_decode($response, true);

        if(isset($response['result']['items'][0])){
            foreach ($response['result']['items'][0] as $url){
                $url = current($url);
                $urls[] = $url['name'];
            }
        }
        return $urls;
    }

    private function _match_article_id($url)
    {
        $pattern1 = '/m\/(\d+)_(\d+)/';
        $pattern2 = '/xs\/(\d+)/';

        if (preg_match($pattern1, $url, $matches)) {
            return isset($matches[2]) ? $matches[2] : '';
        } else {
            preg_match($pattern2, $url, $matches);
            return isset($matches[1]) ? $matches[1] : '';
        }
    }

}
