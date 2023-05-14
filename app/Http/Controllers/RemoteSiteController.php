<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HttpProxyService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class RemoteSiteController extends Controller
{
    /**
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function view(Request $request)
    {
        $target_url = $request->get('target', '');

        /** @var HttpProxyService $httpProxyService */
        $httpProxyService = app("HttpProxyService");
        $proxy = $httpProxyService->proxy();
        $proxy_url = $httpProxyService->proxy_url($proxy);

        $client = new Client([
            'proxy' => $proxy_url,
            'headers' => ['User-Agent' => "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Maxthon/4.9.5.1000 Chrome/39.0.2146.0 Safari/537.36"]
        ]);


        if(strpos('?', $target_url)){
            $target_url .= '&time=' . time();
        }else{
            $target_url .= '?time=' . time();
        }

        $response = $client->get($target_url);

        echo ($response->getBody());
    }
}
