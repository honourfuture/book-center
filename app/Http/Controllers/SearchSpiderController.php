<?php

namespace App\Http\Controllers;


use App\Models\Article;
use App\Models\HandArticle;
use App\Models\NginxAccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchSpiderController extends Controller
{
    public function spider_articles(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $source = $request->get('source');

        $article_logs = NginxAccessLog::with(['article', 'count_access_logs'])
            ->select('source', DB::raw('count(*) as total'), 'date', 'url', 'article_id')
            ->groupBy('article_id', 'source');

        $article_logs->where('date', $date);

        if ($source) {
            $article_logs->where('source', $source);
        }

        $article_logs = $article_logs->orderByDesc('total')->get();

        return view('spider-article-list', ['article_logs' => $article_logs]);
    }

    public function spider_article($id, Request $request)
    {
        $source = $request->get('source');

        $article_logs = NginxAccessLog::where('article_id', $id);

        if ($source) {
            $article_logs->where('source', $source);
        }

        $article_logs = $article_logs

            ->orderByDesc('time')
            ->get();

        $article = Article::find($id);
        return view('spider-article', [
            'article_logs' => $article_logs,
            'article' => $article,
        ]);
    }

    public function set_article_peg()
    {

    }
}
