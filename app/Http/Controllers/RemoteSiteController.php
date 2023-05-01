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
//            'proxy' => $proxy_url
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
