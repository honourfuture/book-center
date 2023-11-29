<?php

namespace App\Http\Controllers;


use App\Enums\RuleEnum;
use App\Enums\SourceEnum;
use App\Models\Article;
use App\Models\HandArticle;
use App\Models\NginxAccessLog;
use App\Models\SourceArticle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\isEmpty;

class SearchSpiderController extends Controller
{

    public function trend_article(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $week_day = date('Y-m-d', strtotime($date . '-7 day'));
        $page_size = $request->get('page_size', 100);

//        $page = NginxAccessLog::select(
//            DB::raw('count(*) as total'),
//            'date', 'url',
//            'article_id'
//        )->groupBy(['article_id'])->orderByDesc('total')->paginate($page_size);;
//
//        $article_ids = $page->pluck('article_id');

        $article_logs = NginxAccessLog::select(
            DB::raw('count(*) as total'),
            'date', 'url',
            'article_id'
        )
            ->groupBy(['article_id', 'remote_addr', 'date']);
        $article_logs = $article_logs
            ->where('date', '<=', $date)
            ->where('date', '>=', $week_day);

        $article_logs = $article_logs->orderByDesc('date')->orderByDesc('total')->get();

        $article_ids = $article_logs->pluck('article_id');
        $articles = Article::select([
            'jieqi_article_article.articleid',
            'jieqi_article_article.author',
            'jieqi_article_article.lastchapter',
            'jieqi_article_article.articlename',
            'jieqi_article_article.lastupdate',
            'jieqi_article_article.fullflag',
        ])->whereIn('articleid', $article_ids)->get()->keyBy('articleid')->toArray();

        $article_log_groups = $article_logs->groupBy('article_id');

        $date_total = [];
        foreach (range(strtotime($date), strtotime($week_day), 86400) as $timestamp) {
            $day = date('Y-m-d', $timestamp);
            $date_total[$day] = 0;
        }

        $spider_articles = [];
        foreach ($article_log_groups as $article_id => $article_logs) {
            $spider_article = [];
            foreach ($article_logs as $log) {
                $date_total[$log['date']] = $log['total'];
            }

            if (isset($articles[$article_id])) {
                $spider_article['article'] = $articles[$article_id];
            }
            $spider_article['total'] = $date_total;
            $spider_articles[$article_id] = $spider_article;
        }

        return view('spider-trend', [
            'spider_articles' => $spider_articles,
            'date_total' => $date_total,
        ]);

    }

    public function spider_articles(Request $request)
    {
        $date = $request->get('date', date('Y-m-d'));
        $source = $request->get('source');
        $hide_check = $request->get('hide_check', 1);

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

        $bind_sources = $this->_get_bind_sources();

        if ($hide_check) {
            $check_article_id = isset($bind_sources['local']) ? $bind_sources['local'] : [];
            $article_logs->whereNotIn('articleid', $check_article_id);
        }

        $article_logs = $article_logs->orderByDesc('total')->get();

        $source_articles = $article_logs->pluck('articlename', 'author')->unique()->toArray();

        $source_artilce_ids = $article_logs->pluck('article_id');
        $source_articles = SourceArticle::whereIn('local_article_id', $source_artilce_ids)->get();

        $source_article_groups = $source_articles->groupBy(function ($article) {
            return md5($article->article_name . '-' . $article->author);
        });

        $sources = SourceEnum::SOURCES;

        $source_article_group_sources = [];
        foreach ($sources as $source) {
            $source_article_group_sources[$source] = [];
        }

        foreach ($article_logs as $log) {
            $md5 = md5($log->articlename . '-' . $log->author);

            if (!isset($source_article_groups[$md5])) {
                continue;
            }

            foreach ($sources as $source) {
                $where_source = $source;
                if ($source == 'do_525uc') {
                    $where_source = '525uc';
                }
                $source_article = $source_article_groups[$md5]->where('source', $where_source)->first();

                if ($source_article) {
                    $source_article_group_sources[$source][] = $source_article->toArray();
                    break;
                }
            }
        }
        return view('spider-article-list', [
            'article_logs' => $article_logs,
            'source_article_groups' => $source_article_groups,
            'source_article_group_sources' => $source_article_group_sources,
            'bind_sources' => $bind_sources,
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

    public function do_low_article($id)
    {
        $sources = $this->_get_bind_sources();
        $sources['local'][] = $id;
        $json_sources = json_encode($sources);
        $storage = Storage::disk();
        $storage->put("/sources.json", $json_sources);

        return response()->view('close-tab');
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

    private function _get_bind_sources()
    {
        $storage = Storage::disk();
        $sources = [];
        if ($storage->exists('/sources.json')) {
            $sources = $storage->get('/sources.json');
            $sources = json_decode($sources, true);
        }
        $all_sources = SourceEnum::SOURCES;
        foreach ($all_sources as $source) {
            if (!isset($sources[$source])) {
                $sources[$source] = [];
            }
        }
        return $sources;
    }

    public function create_sources()
    {
        $sources = $this->_get_bind_sources();
        return view('create-sources', ['sources' => $sources]);
    }

    public function do_create_sources(Request $request)
    {
        $sources = $request->get('sources');

        $json_sources = [];
        foreach ($sources as $source => $ids) {
            $json_sources[$source] = array_filter(array_unique(explode(',', $ids)));
        }
        $json_sources = json_encode($json_sources);
        $storage = Storage::disk();
        $storage->put("/sources.json", $json_sources);

        return redirect()->route('create-sources');
    }

    public function get_artisan(Request $request)
    {
        $sqlite = $request->get('db_name', date('Ymd'));
        $is_spider = $request->get('is_spider', 0);
        $sqlites = explode(',', $sqlite);


        $article_ids = NginxAccessLog::groupBy('article_id')->pluck('article_id')->toArray();

        $sources = ['mayi' => [], 'tt' => [], 'xwbiquge' => [], '00shu' => [], '69shu' => []];

        if (!$is_spider) {
            $taskLogs = [];
            foreach ($sqlites as $sqlite) {
                $taskLog = DB::connection($sqlite)->table('taskLog')
                    ->whereIn('EXID', [120])
                    ->where('TASKFILE', '<>', 'C:\Users\Administrator\Desktop\方案\kdzw\kdzw_go.xml')
                    ->whereIn('RULEFILE', RuleEnum::MAYI_AUTO)
                    ->get()
                    ->toArray();

                $taskLogs = array_merge($taskLogs, $taskLog);
            }
            $sources['mayi'] = array_column($taskLogs, 'NID');
        }
        $articles = Article::select([
            'jieqi_article_article.articleid',
            'jieqi_article_article.author',
            'jieqi_article_article.lastchapter',
            'jieqi_article_article.articlename',
            'jieqi_article_article.lastupdate'
        ])->whereIn('articleid', $article_ids)->get()->keyBy(function ($article) {
            return md5($article->articlename . '-' . $article->author);
        });

        $article_id_groups = array_chunk($article_ids, 200);

        foreach ($article_id_groups as $article_ids) {
            $article_ids = array_filter($article_ids);
            $source_articles = SourceArticle::whereIn('local_article_id', $article_ids)
                ->whereIn('source', array_keys($sources))
                ->get();

            $source_article_groups = $source_articles->groupBy(function ($article) {
                return md5($article->article_name . '-' . $article->author);
            });


            foreach ($source_article_groups as $key => $source_articles) {
                if ($key == '3f8ca62a6da6d8b78a195cf4b5f1e20b') {
                    continue;
                }
                foreach ($source_articles as $source_article) {
                    $sources[$source_article->source][] = $articles[$key]['articleid'];
                }
            }
        }


        $artisans = [];
        foreach ($sources as $source => $article_ids) {
            $count = count(array_unique($article_ids));
            $article_ids = implode(',', array_unique($article_ids));
            $artisans[] = [
                'artisan' => sprintf('php74 artisan push:article --site=%s --article_ids=%s', $source, $article_ids),
                'count' => $count,
            ];
        }
        return view('spider-artisan', [
            'artisans' => $artisans,
        ]);


    }
}
