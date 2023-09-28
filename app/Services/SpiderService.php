<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 17:43
 */

namespace App\Services;


use App\Models\Article;
use App\Models\OriginArticleId;
use App\Models\SourceArticle;
use QL\QueryList;

class SpiderService
{
    private $config;

    private $source;

    public function __construct($param)
    {
        $site = $param['site'];
        $this->source = $site;
        $site_config = config("spider");
        $this->config = $site_config[$site];
    }

    public function get_article_info($url)
    {
        /** @var HttpProxyService $httpProxyService */
        $httpProxyService = app('HttpProxyService');

        $user_agent = $httpProxyService->user_agent();
        // 元数据DOM解析规则
        $rules = [
            'book_name' => ['meta:eq(11)', 'content'],
            'author' => ['meta:eq(10)', 'content'],
            'desc' => ['#intro', 'text']
        ];

        $rules = $this->config['get_article_info_rule'];

        $rt = QueryList::get($url, [], [
            'headers' => [
                'User-Agent' => $user_agent,
                'Accept-Encoding' => 'gzip, deflate, br',
            ]
        ]);
        $rt = $rt->rules($rules)->query()->getData();

        return $rt->all();
    }


    public function get_article($url)
    {
        $html = file_get_contents($url);

        if ($this->config['charset'] == 'gbk') {
            $html = iconv('gbk', 'utf-8//IGNORE', $html);
        }

        // 元数据DOM解析规则
        $rules = [
            // DOM解析文章标题
            'chapters' => ['a', 'texts'],
            // DOM解析链接
            'chapter_hrefs' => ['a', 'attrs(href)'],
        ];

        $rules = $this->config['get_article_rule'];
        $range = $this->config['get_article_range'];

        $rt = QueryList::html($html)->rules($rules)
            ->range($range)->query()->getData();

        $data = $rt->all();

        if ($data) {
            foreach ($data[0]['chapter_hrefs'] as &$href) {
                $href = $this->config['url'] . $href;
            }
        }

        return $data;
    }

    public function get_chapter($url, $content = '')
    {
        /** @var HttpProxyService $httpProxyService */
        $httpProxyService = app('HttpProxyService');
        $user_agent = $httpProxyService->user_agent();
        $html = file_get_contents($url);

        if ($this->config['charset'] == 'gbk') {
            $html = iconv('gbk', 'utf-8//IGNORE', $html);
        }

        $find = $this->config['get_chapter_find'];
        $text = QueryList::html($html)->find($find)->html();
        $content .= $this->config['content_preg']($text);

        if (isset($this->config['next_page'])) {
            $next_page = $this->config['next_page'];
            $next_info = QueryList::html($html)->rules($next_page['rule'])->range($next_page['range'])->query()->getData();

            if($next_info[0]['text'][0] == $this->config['next_page']['has_text']){
                $next_url = $this->config['url'] . $next_info[0]['url'][0];
                $content = $this->get_chapter($next_url, $content);
            }
        }

        sleep(2);

        return $content;
    }

    /**
     * @param $article_id
     * @return string
     */
    public function build_article_url($article_id)
    {
        $index = intval($article_id / 1000);
        $url = $this->config['article_url'];
        if (strpos('{--index--}', $url) !== false) {

        }
        $url = str_replace('{--index--}', $index, $url);
        $url = str_replace('{--article_id--}', $article_id, $url);

        return $url;
    }

    /**
     * @param $article
     * @return string
     */
    public function get_origin_url($article)
    {
        $key = $this->config['name'];

        $source_article = SourceArticle::where('article_name', $article->articlename)
            ->where('author', $article->author)
            ->where('source', $key)->first();

        if (!$source_article) {
            return false;
        }

        return $this->build_article_url($source_article->article_id);


        $origin_articles = SourceArticle::where('article_name', $article->articlename)->get();

        $origin_article = $origin_articles->where('author', $article->author)->first();

        if ($origin_article) {
            return $this->build_article_url($origin_article->article_id);
        }

        foreach ($origin_articles as $origin_article) {
            if ($origin_article->author) {
                continue;
            }

            $url = $this->build_article_url($origin_article->article_id);

            $article_info = $this->get_article_info($url);

            if ($article_info) {
                $article_info['author'] = remove_space($article_info['author']);
                $article_info['desc'] = remove_space($article_info['desc']);

                SourceArticle::where('article_id', $origin_article->article_id)->update([
                    'author' => $article_info['author'],
                    'desc' => $article_info['desc'],
                ]);
                if ($article_info['author'] == $article['author']) {
                    if (isset($article_info['desc']) && $article_info['desc']) {
                        Article::where('articleid', $article->articleid)->update([
                            'intro' => $article_info['desc']
                        ]);
                    }

                    return $this->build_article_url($origin_article->article_id);
                }
            }
        }

        return false;
    }

}
