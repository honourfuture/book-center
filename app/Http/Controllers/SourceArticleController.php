<?php

namespace App\Http\Controllers;


use App\Models\SourceArticle;
use Illuminate\Http\Request;

class SourceArticleController extends Controller
{
    /**
     * @param Request $request
     */
    public function add_source_article(Request $request)
    {
        $max_id = $request->get('max_id');
        $source = $request->get('source');

        if(!$max_id || !$source){
            echo "max_id source is not empty";
            exit;
        }

        $urls = [
            'mayi' => 'https://www.mayiwxw.com/109_{article_id}/index.html',
            'tt' => 'https://www.ttshuba.org/info-{article_id}.html',
            'xwbiquge' => 'http://www.xwbiquge.com/biquge_{article_id}/',
        ];

        $article_ids = [];
        for($i = 1; $i <= $max_id; $i++){
            $article_ids[] = $i;
        }

        $source_article_ids = SourceArticle::select('article_id')->where('source', $source)->pluck('article_id')->toArray();

        $article_ids = array_diff($article_ids, $source_article_ids);

        $url = $urls[$source];
        foreach ($article_ids as $article_id){
            echo str_replace('{article_id}', $article_id, $url)."<br/>";
        }
    }
}