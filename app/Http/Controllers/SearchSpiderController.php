<?php

namespace App\Http\Controllers;


use App\Models\Article;
use App\Models\HandArticle;
use App\Models\NginxAccessLog;
use Carbon\Carbon;
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
