<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/11/29
 * Time: 17:07
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

class Search extends Controller
{
    public function search_articles(Request $request)
    {
        $keyword = $request->get('keyword');

        if(!$keyword){
            return [];
        }

        $secret = $request->get('secret');

        $client = ClientBuilder::create()
            ->setHosts([config('database.connections.elasticsearch.host')])
            ->setBasicAuthentication(config('database.connections.elasticsearch.user'), config('database.connections.elasticsearch.pass'))
            ->build();

        $params = [
            'index' => config('database.connections.elasticsearch.index'),
            'body' => [
                'query' => [
                    'bool' => [
                        'should' => [
                            [
                                'match_phrase' => [
                                    'article_name' => $keyword
                                ]
                            ],
                            [
                                'match_phrase' => [
                                    'author' => $keyword
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $article_ids = [];

        try {
            $res = $client->search($params);
            if (isset($res['hits']['hits'])) {
                $article_ids = array_column($res['hits']['hits'], '_id');
            }
        } catch (\Exception $exception) {
            logger($exception);
        }

        return response()->json($article_ids);
    }
}
