<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\BookUpdateArticle;
use App\Models\SourceArticle;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use QL\QueryList;

class CollectArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'collect:article {--site=} {--page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'source:update';

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
     **/
    public function handle()
    {
        $site = $this->option('site') ?: 'mayi';
        $page = $this->option('page') ?: 'home';

        //config 获取列表页
        //入库 字段 : article_name author local_article_id last_chapter md5(article_name author last_chapter) created_at updated_at status(0更新 1新增)
        //目的: 提供快捷不漏的更新/添加  自动校对30章 source_articles 的补充数据

        $config = config('spider-article')[$site];

        $page = $config['pages'][$page];

        $html = $this->_origin($page['url']);

        if ($config['charset'] == 'gbk') {
            $html = iconv('gbk', 'utf-8//IGNORE', $html);
        }

        $rules = $page['rule'];
        $range = $page['range'];

        $rt = QueryList::html($html)->rules($rules)
            ->range($range)->query()->getData();

        $update_articles = $rt->all();

        $rules = $page['add_rule'];
        $range = $page['add_range'];

        $rt = QueryList::html($html)->rules($rules)
            ->range($range)->query()->getData();

        $add_articles = $rt->all();
        $insert_articles = [];

        $update_articles = $update_articles[0];
        $add_articles = $add_articles[0];
        $article_names = [];
        $authors = [];
        $article_ids = [];

        for ($i = 0; $i < 30; $i++) {

            $article_name = $update_articles['article_names'][$i];
            $article_url = $update_articles['article_urls'][$i];
            $author = $update_articles['authors'][$i];
            $last_chapter = $update_articles['last_chapters'][$i];

            $article_id = $this->_match_article_id($article_url);

            if($last_chapter == '该章节已被锁定'){
                continue;
            }

            $add_article_name = $add_articles['article_names'][$i];
            $add_article_url = $add_articles['article_urls'][$i];
            $add_author = $add_articles['authors'][$i];
            $add_article_id = $this->_match_article_id($add_article_url);
            $key = md5($article_name . '-' . $author);
            $insert_articles[$key] = [
                'article_name' => $article_name,
                'article_url' => $article_url,
                'author' => $author,
                'last_chapter' => $last_chapter,
                'article_id' => $article_id,
                'unique_md5' => md5($article_name . '-' . $author),
                'site' => $site,
                'type' => 1,
            ];

            $key = md5($add_article_name . '-' . $add_author);
            $insert_articles[$key] = [
                'article_name' => $add_article_name,
                'article_url' => $add_article_url,
                'author' => $add_author,
                'last_chapter' => '新增',
                'article_id' => $add_article_id,
                'unique_md5' => md5($add_article_name),
                'site' => $site,
                'type' => 2,
            ];

            $article_names = array_merge([$add_article_name, $article_name], $article_names);
            $authors = array_merge([$add_author, $author], $authors);
            $article_ids = array_merge([$add_article_id, $article_id], $article_ids);
        }

        // 新增至source_article
        $source_articles = SourceArticle::whereIn('article_id', $article_ids)
            ->where('source', $site)
            ->get()->keyBy(function ($article) {
            return md5($article->article_name . '-' . $article->author);
        })->toArray();

        //查询
        $articles = Article::select(['articleid', 'articlename', 'author'])->whereIn('articlename', $article_names)
            ->whereIn('author', $authors)->get()->keyBy(function ($article) {
                return md5($article->articlename . '-' . $article->author);
            })->toArray();

        $insert_source_articles = [];
        foreach ($insert_articles as &$article){
            $unique = md5($article['article_name'] . '-' . $article['author']);
            $local_article_id = isset($articles[$unique]) ? $articles[$unique]['articleid'] : 0;
            $article['local_article_id'] = $local_article_id;

            if(!isset($source_articles[$unique])){
                $insert_source_articles[] = [
                    'article_name' => $article['article_name'],
                    'local_article_id' => $article['local_article_id'],
                    'author' => $article['author'],
                    'article_id' => $article['article_id'],
                    'origin_url' => $article['article_url'],
                    'source' => $site
                ];
            }
        }

        BookUpdateArticle::insertIgnore($insert_articles);
        SourceArticle::insert($insert_source_articles);

        echo date('Y-m-d H:i:s', time());
    }

    private function _match_article_id($url)
    {
        preg_match('/\/\d+_(\d+)/', $url, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        } else {
            return 0;
        }
    }

    /**
     * @param $url
     * @return \Psr\Http\Message\StreamInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function _origin($url)
    {
        $client = new Client([
            'base_uri' => $url,
            'timeout' => 6.0
        ]);

        $options = [
            'query' => [
                'time' => time(),
            ]
        ];

        $response = $client->request('GET', '', $options);

        return $response->getBody();
    }
}
