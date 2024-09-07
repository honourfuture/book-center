<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Elasticsearch\ClientBuilder;

class CreateEsArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'es-article:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建article es';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $client = ClientBuilder::create()
            ->setHosts([config('database.connections.elasticsearch.host')])
            ->setBasicAuthentication(config('database.connections.elasticsearch.user'), config('database.connections.elasticsearch.pass'))
            ->build();

        Article::select(['articleid', 'author', 'articlename'])->where('articleid', '>', 133421)->chunk(200, function ($articles) use ($client) {
            $params = [
                'body' => []
            ];
            $documents = [];

            foreach ($articles as $article) {
                $documents[] = [
                    'index' => [
                        '_index' => config('database.connections.elasticsearch.index'),
                        '_id' => $article->articleid,
                    ]
                ];
                $documents[] = [
                    'author' => $article->author,
                    'article_name' => $article->articlename
                ];
            }

            $params['body'] = $documents;

            try {
                $response = $client->bulk($params);

            } catch (\Exception $exception) {
                $response = $exception->getMessage();
            }

            $this->info(json_encode($response));
        });

//        $params = [
//            'index' => 'tieshuw',
//            'body' => [
//                'settings' => [
//                    'number_of_shards' => 1,
//                    'number_of_replicas' => 1,
//                ],
//
//                'mappings' => [
//                    '_source' => [
//                        'enabled' => true
//                    ],
//                    'properties' => [
//                        'article_name' => [
//                            "type" => "text",
//                        ],
//                        'author' => [
//                            "type" => "text",
//                        ],
//                        'article_id' => [
//                            'type' => 'integer'
//                        ]
//                    ]
//                ],
//            ]
//        ];
//
//        try {
//            $res = $client->indices()->create($params);
//        }catch (\Exception $exception){
//            $res = $exception->getMessage();
//        }
//        $docsToDelete = [
//            ['_index' => config('database.connections.elasticsearch.index'), '_id' => 1],
//            ['_index' => config('database.connections.elasticsearch.index'), '_id' => 2],
//            ['_index' => config('database.connections.elasticsearch.index'), '_id' => 3],
//        ];
//
//        $params = [
//            'body' => []
//        ];
//
//        foreach ($docsToDelete as $doc) {
//            $params['body'][] = [
//                'delete' => $doc
//            ];
//        }
//        $response = $client->bulk($params);
    }

}
