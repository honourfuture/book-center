<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\ArticleKeyword;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

use Illuminate\Console\Command;

class BuildArticleKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build:seo {--article_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
        $article_id = $this->option('article_id');

        $select = ['articleid', 'articlename', 'author', 'chapters'];
        $article_keyword_article_ids = ArticleKeyword::groupBy('articleid')->pluck('articleid')->toArray();

        if ($article_id) {
            $articles = Article::select($select)
                ->whereNotIn('articleid', $article_keyword_article_ids)
                ->where('articleid', $article_id)
                ->get();
        } else {
            $articles = Article::select($select)->where('chapters', '>', 100)
                ->whereNotIn('articleid', $article_keyword_article_ids)
                ->limit(50)
                ->get();
        }
        foreach ($articles as $article) {

            $response = $this->_get_baidu_search_keywords($article->articlename);
            $keywords = $this->decode_do_json($response);

            if (isset($keywords['s']) && $keywords) {
                $search_keywords = $keywords['s'];
            } else {
                $search_keywords = [
                    "{$article->articlename}",
                    "{$article->articlename}下载",
                    "{$article->articlename}晋江",
                    "{$article->articlename}凤冲霄",
                    "{$article->articlename}TXT下载",
                    "{$article->articlename}免费阅读",
                    "{$article->articlename}免费",
                    "{$article->articlename}起点",
                    "{$article->articlename}笔趣阁",
                    "{$article->articlename}潇湘",
                ];
            }

            $this->_insert_article_keywords($article, $search_keywords);

        }
    }


    private function decode_do_json($json_code)
    {
        $json_code = iconv('gbk', 'utf-8//IGNORE', $json_code);
        preg_match('/\{(.+?)\}/', $json_code, $matches);
        $json_string = '{' . $matches[1] . '}';
        $json_string = preg_replace('/(\w+):/', '"$1":', $json_string);
        return json_decode($json_string, true);
    }

    private function _insert_article_keywords($article, $keywords = []): array
    {
        if (!$keywords) {
            return [];
        }

        $article_keywords = [];

        $add_1 = "{$article->articlename}TXT下载";
        $add_2 = "{$article->articlename}txt下载";
        $add_3 = "{$article->articlename}TXT";
        $add_4 = "{$article->articlename}txt";

        if (!in_array($add_1, $keywords)) {
            $keywords[] = $add_1;
        }
        if (!in_array($add_2, $keywords)) {
            $keywords[] = $add_2;
        }
        if (!in_array($add_3, $keywords)) {
            $keywords[] = $add_3;
        }
        if (!in_array($add_4, $keywords)) {
            $keywords[] = $add_4;
        }
        foreach ($keywords as $keyword) {

            $article_keywords[] = [
                'keyword' => $keyword,
                'articleid' => $article->articleid,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        ArticleKeyword::insert($article_keywords);

        return [];
    }


    private function _get_baidu_search_keywords($wd)
    {
        $client = new Client([
            'base_uri' => 'https://sp1.baidu.com',
            'timeout' => 3.0
        ]);

        $response = $client->request('GET', '/5a1Fazu8AA54nxGko9WTAnF6hhy/su', [
            'query' => [
                'wd' => $wd,
                'cb' => 'doJson'
            ]
        ]);

        return $response->getBody()->getContents();
    }
}
