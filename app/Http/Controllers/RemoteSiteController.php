<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\HttpProxyService;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;

class RemoteSiteController extends Controller
{
    /**
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function view(Request $request)
    {
        $target_url = $request->get('target', '');
        $cookie = $request->get('is_cookie', '727abdc9131d272e');

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

        if($cookie){
            $cookieJar = CookieJar::fromArray(['nxgmnmry' => $cookie],'www.80y.net');
            $response = $client->request('GET', $target_url, ['cookies' => $cookieJar]);
            echo ($response->getBody());
            exit;
        }

        $response = $client->get($target_url);
        echo ($response->getBody());
    }

    public function get_origin_view_cookie(Request $request)
    {
        $url = $request->get('url');
        if(strpos('?', $url)){
            $url .= '&time=' . time();
        }else{
            $url .= '?time=' . time();
        }

        $html = Browsershot::url($url)
            ->windowSize(480, 800)
            ->userAgent('Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.132 Mobile Safari/537.36')
            ->mobile()
            ->touch()
            ->bodyHtml();

        print_r($html);die;
    }
}
