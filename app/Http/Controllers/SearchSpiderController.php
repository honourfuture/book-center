<?php

namespace App\Http\Controllers;


use App\Models\Article;
use App\Models\HandArticle;
use App\Models\NginxAccessLog;
use App\Models\SourceArticle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchSpiderController extends Controller
{
    public function spider_articles(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $source = $request->get('source');

        $article_logs = NginxAccessLog::with(['count_access_logs'])
            ->leftJoin('jieqi_article_article', 'jieqi_article_article.articleid', '=', 'nginx_access_logs.article_id')
            ->select(
                DB::raw('count(*) as total'),
                'date', 'url',
                'article_id', 'jieqi_article_article.articleid',
                'jieqi_article_article.author',
                'jieqi_article_article.lastchapter',
                'jieqi_article_article.articlename',
                'jieqi_article_article.lastupdate')
            ->groupBy('article_id');

        if ($source) {
            $article_logs->where('source', $source);
        }


        $article_logs = $article_logs->orderByDesc('total')->get();

        $source_articles = $article_logs->pluck('articlename', 'author')->unique()->toArray();


        $source_articles = SourceArticle::whereIn('author', array_filter(array_keys($source_articles)))
            ->whereIn('article_name', array_filter(array_values($source_articles)))
            ->get();

        $source_article_groups = $source_articles->groupBy(function ($article) {
                return md5($article->article_name . '-' .$article->author);
            });

        $source_article_group_sources = $source_articles->groupBy(['source']);

        return view('spider-article-list', [
            'article_logs' => $article_logs,
            'source_article_groups' => $source_article_groups,
            'source_article_group_sources' => $source_article_group_sources
        ]);
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

    public function spider_statics()
    {
        $sources = DB::table('nginx_access_logs')
            ->select('source')
            ->groupBy('source')
            ->get()
            ->pluck('source')
            ->toArray();

        $logs = DB::table('nginx_access_logs')
            ->select('date', 'source', DB::raw('COUNT(*) as total'))
            ->where('date', '>=', Carbon::now()->subDays(30)->format('Y-m-d'))
            ->groupBy('date', 'source')
            ->orderBy('date', 'desc')
            ->get();

        $dates = [];
        $data = [];

        foreach ($logs as $log) {
            $date = $log->date;
            $source = $log->source;
            $total = $log->total;

            if (!in_array($date, $dates)) {
                $dates[] = $date;
            }

            if (!isset($data[$date])) {
                $data[$date] = array_fill_keys($sources, 0);
            }

            $data[$date][$source] = $total;
        }

        $previousData = [];
        foreach ($dates as $date) {
            $previousDate = Carbon::parse($date)->subDay()->format('Y-m-d');
            if (isset($data[$previousDate])) {
                $previousData[$date] = $data[$previousDate];
            } else {
                $previousData[$date] = array_fill_keys($sources, 0);
            }
        }

        $result = [];
        foreach ($dates as $date) {
            $result[$date] = [];

            foreach ($sources as $source) {
                $total = $data[$date][$source];
                $previousTotal = $previousData[$date][$source];

                $arrowClass = '';
                if ($total > $previousTotal) {
                    $arrowClass = 'arrow-up red';
                } elseif ($total < $previousTotal) {
                    $arrowClass = 'arrow-down green';
                }

                $result[$date][$source] = [
                    'total' => $total,
                    'arrowClass' => $arrowClass,
                ];
            }
        }

        return view('spider-statics', ['data' => $result, 'sources' => $sources]);
    }
}
