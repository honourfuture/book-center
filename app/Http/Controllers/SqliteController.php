<?php

namespace App\Http\Controllers;

use App\Enums\RuleEnum;
use App\Models\Article;
use App\Models\SourceArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SqliteController extends Controller
{
    public function get_tieshu_list(Request $request)
    {
        $sqlite = $request->get('db_name', date('Ymd'));
        $sqlites = explode(',', $sqlite);
        $is_all = $request->get('is_all', 1);
        $max_date = $request->get('max_date', date('Y-m-d 00:00:00'));
        $min_date = $request->get('min_date', date('1970-01-01 00:00:00'));

        $all_ids = [];

        $rules = [
            'mayi' => RuleEnum::MA_YI,
            'tt' => RuleEnum::TT,
            'lwxs' => ['Rules\S_www_lwxs_com.xml'],
            '4ksw' => ['Rules\lewen2_com_gz.xml', 'Rules\4ksw_com.xml'],
            'bixia' => ['Rules\v2_bixia66.xml'],
            'bqg789_co' => ['Rules\p1_bqg789_co.xml', 'Rules\bqg789_co.xml'],
            '69shu' => ['Rules\p1_69shu_com.xml'],
            'meigui' => ['Rules\jj_meiguixs_net.xml', 'Rules\jj_meiguixs_net_v1.xml', 'Rules\jj_meiguixs_net_v2.xml'],
            'kdzw' => ['Rules\kdzw_net.xml'],
            '9it' => ['Rules\9itan_cc.xml'],
            'biquge5200' => ['Rules\biquge5200_cc.xml'],
        ];

        $rule_ids = [];

        $max_date = strtotime($max_date);
        $min_date = strtotime($min_date);

        foreach ($rules as $rule_name => $rule) {
            $taskLogs = [];
            foreach ($sqlites as $sqlite) {
                $taskLog = DB::connection($sqlite)->table('taskLog')
                    ->whereIn('EXID', [120])
                    ->where('TASKFILE', '<>', 'C:\Users\Administrator\Desktop\方案\kdzw\kdzw_go.xml')
                    ->whereIn('RULEFILE', $rule)
                    ->get()
                    ->toArray();

                $taskLogs = array_merge($taskLogs, $taskLog);
            }

            $nids = array_column($taskLogs, 'NID');
            $articles = Article::select(['articleid', 'lastupdate'])->whereIn('articleid', $nids)->get()->keyBy('articleid')->toArray();
            foreach ($taskLogs as $log) {
                if (in_array($log->NID, $all_ids)) {
                    continue;
                }
                $all_ids[] = $log->NID;

                $last_update = isset($articles[$log->NID]) ? $articles[$log->NID]['lastupdate'] : time();
                if (!$is_all) {
                    if ($last_update >= $max_date) {
                        continue;
                    }
                    if ($last_update <= $min_date) {
                        continue;
                    }
                }

                $rule_ids[$rule_name]['origin'][$log->GETID] = date('Y-m-d H:i:s', $last_update);
                $rule_ids[$rule_name]['local'][] = $log->NID;
            }
        }

        foreach ($rule_ids as $rule_name => $rule) {
            $count = count(array_keys($rule['origin']));
            echo $rule_name . "({$count}):\n";
            asort($rule['origin']);
            echo implode(',', array_keys($rule['origin'])) . "\n";
        }
    }

    public function get_tieshu()
    {
        $taskLog = DB::connection('20230725')->table('taskLog')
            ->whereIn('EXID', [120])
            ->where('TASKFILE', '<>', 'C:\Users\Administrator\Desktop\方案\kdzw\kdzw_go.xml')
            ->where('RULEFILE', 'Rules\kdzw_net.xml')
            ->get()
            ->toArray();

        foreach ($taskLog as $log) {
            $update[] = $log->GETID;
        }

        print_r(implode(',', $update));
    }

    public function get_empty_article()
    {
        $article_ids = $this->_articles();
        $all_sqlite = config('database.all_sqlite');
        $update = [];
        $update_local = [];

        $nids = [];
        foreach ($all_sqlite as $sqlite) {
            foreach ($article_ids as $ids) {
                $taskLog = DB::connection($sqlite)->table('taskLog')
                    ->whereNotIn('EXID', [130, 132])
                    ->whereIn('NID', $ids)
                    ->get()
                    ->toArray();
                foreach ($taskLog as $log) {
//                    $update[$log->RULEFILE][] = $log->GETID .'|'. $log->TASKFILE.'|'. $log->EXID;
                    $update[$log->RULEFILE][] = $log->GETID;
                    $update_local[$log->RULEFILE][] = [$log->GETID => $log->NID];

                }
            }
        }
        $updateRule = [];
        foreach ($update as $key => $ids) {
            $article_ids = array_chunk(array_unique($ids), 500);
            foreach ($article_ids as $id) {
                $updateRule[$key][] = implode(',', $id);
            }

        }

        print_r($updateRule);
    }

    private function _articles()
    {

        $article_ids = [69530, 63685, 30705, 64125, 72739, 64679, 80783, 16418, 72824, 29931, 1955, 10351, 73163, 73865, 27117, 67848, 73164, 65652, 75619, 2660, 45349, 61284, 64275, 68105, 73068, 18236, 23702, 62447, 73251, 67575, 35820, 59854, 62048, 34274, 58114, 64020, 73658, 64434, 12401, 21878, 39772, 62440, 64120, 73143, 111, 1406, 63883, 69806, 69826, 11420, 60457, 65485, 1073, 1286, 2701, 3018, 61799, 6032, 18664, 27559, 64357, 80407, 2562, 19768, 47478, 57950, 63343, 73, 2205, 4087, 11241, 26446, 50002, 61372, 62529, 77849, 2130, 4454, 5925, 22965, 26435, 28917, 30569, 33221, 38444, 47343, 47416, 50025, 54475, 57484, 57736, 62422, 68612, 70253, 1124, 1636, 1657, 2066, 3095, 12241, 21638, 26501, 30723, 33063, 43475, 54276, 56552, 60067, 62346, 61850, 74592, 74732, 80235, 49, 518, 67245, 1060, 1776, 7654, 14592, 14682, 15249, 15781, 16048, 18147, 22601, 23052, 25411, 26183, 26264, 26604, 28785, 30032, 30065, 30100, 33494, 36010, 38728, 41004, 48148, 48197, 48473, 49943, 51034, 54088, 55679, 57304, 57372, 57661, 60440, 60739, 60933, 61531, 63135, 63367, 64923, 65662, 65397, 66317, 67710, 69357, 69682, 69906, 72009, 73864, 74316, 76907, 78465, 6124, 33210, 49918, 64698, 71698];
        return array_chunk($article_ids, 50);
    }

    public function get_mayi_list(Request $request)
    {
        $sqlite = $request->get('db_name', date('Ymd'));
        $sqlites = explode(',', $sqlite);
        $source = $request->get('source', 'mayi');
        $max_date = $request->get('max_date', date('Y-m-d 00:00:00'));
        $min_date = $request->get('min_date', date('1970-01-01 00:00:00'));
        $is_all = $request->get('is_all', 1);

        $rules = [
            'mayi' => RuleEnum::MA_YI_120,
        ];

        $rule_ids = [];

        $max_date = strtotime($max_date);
        $min_date = strtotime($min_date);

        $rule = $rules[$source];

        $taskLogs = [];
        foreach ($sqlites as $sqlite) {
            $taskLog = DB::connection($sqlite)->table('taskLog')
                ->whereIn('EXID', [120])
                ->whereIn('RULEFILE', $rule)
                ->get()
                ->toArray();

            $taskLogs = array_merge($taskLogs, $taskLog);
        }

        $nids = array_column($taskLogs, 'NID');
        $source_articles = Article::select(['articleid', 'lastupdate', 'articlename', 'author'])->whereIn('articleid', $nids)->get();
        $articles = $source_articles->keyBy('articleid')->toArray();

        $source_articles = $source_articles->pluck('articlename', 'author')->unique()->toArray();
        $source_articles = SourceArticle::whereIn('author', array_filter(array_keys($source_articles)))
            ->whereIn('article_name', array_filter(array_values($source_articles)));

        $source_articles = $source_articles->where('source', 'mayi');
        $source_articles = $source_articles->get();

        $source_article_groups = $source_articles->keyBy(function ($article) {
            return md5($article->article_name . '-' . $article->author);
        });

        foreach ($taskLogs as $log) {
            $all_ids[] = $log->NID;

            if (!isset($articles[$log->NID])) {
                continue;
            }
            $article = $articles[$log->NID];
            $md5 = md5($article['articlename'] . '-' . $article['author']);

            $last_update = isset($article) ? $article['lastupdate'] : time();
            if (!$is_all) {
                if ($last_update >= $max_date) {
                    continue;
                }
                if ($last_update <= $min_date) {
                    continue;
                }
            }

            if (!isset($source_article_groups[$md5])) {
                if(in_array($log, [
                    'Rules\A_xs5300_net.xml',
                    'Rules\A_biqusk_com.xml'
                ])){
                    continue;
                }
            }
            $gid = $log->GETID;
            if(isset($source_article_groups[$md5])){
                $gid = $source_article_groups[$md5]['article_id'];
            }

            $rule_ids[$gid] = date('Y-m-d H:i:s', $last_update);
        }

        asort($rule_ids);
        echo implode(',', array_keys($rule_ids));
    }

    public function get_source_list(Request $request)
    {
        $sqlite = $request->get('db_name', date('Ymd'));
        $sqlites = explode(',', $sqlite);
        $source = $request->get('meigui', 'meigui');
        $max_date = $request->get('max_date', date('Y-m-d 00:00:00'));
        $min_date = $request->get('min_date', date('1970-01-01 00:00:00'));
        $is_all = $request->get('is_all', 1);

        $rules = [
            'mayi' => RuleEnum::MA_YI,
            'tt' => RuleEnum::TT,
            'lwxs' => ['Rules\S_www_lwxs_com.xml'],
            '4ksw' => ['Rules\lewen2_com_gz.xml', 'Rules\4ksw_com.xml'],
            'bixia' => ['Rules\v2_bixia66.xml'],
            'bqg789_co' => ['Rules\p1_bqg789_co.xml', 'Rules\bqg789_co.xml'],
            '69shu' => ['Rules\p1_69shu_com.xml'],
            'meigui' => ['Rules\jj_meiguixs_net.xml', 'Rules\jj_meiguixs_net_v1.xml', 'Rules\jj_meiguixs_net_v2.xml'],
            'kdzw' => ['Rules\kdzw_net.xml'],
            '9it' => ['Rules\9itan_cc.xml'],
            'biquge5200' => ['Rules\biquge5200_cc.xml'],
        ];

        $rule_ids = [];

        $max_date = strtotime($max_date);
        $min_date = strtotime($min_date);

        $rule = $rules[$source];

        $taskLogs = [];
        foreach ($sqlites as $sqlite) {
            $taskLog = DB::connection($sqlite)->table('taskLog')
                ->whereIn('EXID', [120])
                ->whereIn('RULEFILE', $rule)
                ->get()
                ->toArray();

            $taskLogs = array_merge($taskLogs, $taskLog);
        }

        $nids = array_column($taskLogs, 'NID');
        $source_articles = Article::select(['articleid', 'lastupdate', 'articlename', 'author'])->whereIn('articleid', $nids)->get();
        $articles = $source_articles->keyBy('articleid')->toArray();

        $source_articles = $source_articles->pluck('articlename', 'author')->unique()->toArray();
        $source_articles = SourceArticle::whereIn('author', array_filter(array_keys($source_articles)))
            ->whereIn('article_name', array_filter(array_values($source_articles)));

        if ($source == 'meigui') {
            $source_articles = $source_articles->where('source', '<>', '9it');
        }

        $source_articles = $source_articles->get();

        $source_article_groups = $source_articles->groupBy(function ($article) {
            return md5($article->article_name . '-' . $article->author);
        });

        foreach ($taskLogs as $log) {
            $all_ids[] = $log->NID;

            if (!isset($articles[$log->NID])) {
                continue;
            }
            $article = $articles[$log->NID];
            $md5 = md5($article['articlename'] . '-' . $article['author']);

            $last_update = isset($article) ? $article['lastupdate'] : time();
            if (!$is_all) {
                if ($last_update >= $max_date) {
                    continue;
                }
                if ($last_update <= $min_date) {
                    continue;
                }
            }

            if (isset($source_article_groups[$md5])) {
                $other = $source_article_groups[$md5]->where('source', '<>', $source)->first();
                if ($other) {
                    continue;
                }
            }

            $rule_ids[$log->GETID] = date('Y-m-d H:i:s', $last_update);
        }

        asort($rule_ids);
        echo implode(',', array_keys($rule_ids));
    }

    public function get_auto_replace(Request $request)
    {
        $sqlite = $request->get('db_name', date('Ymd'));
        $sqlites = explode(',', $sqlite);
        $is_all = $request->get('is_all', 1);
        $max_date = $request->get('max_date', date('Y-m-d 00:00:00'));
        $min_date = $request->get('min_date', date('1970-01-01 00:00:00'));
        $all_ids = [];

        $rules = [
            'mayi' => RuleEnum::MAYI_AUTO,
        ];

        $rule_ids = [];

        $max_date = strtotime($max_date);
        $min_date = strtotime($min_date);

        foreach ($rules as $rule_name => $rule) {
            $taskLogs = [];
            foreach ($sqlites as $sqlite) {
                $taskLog = DB::connection($sqlite)->table('taskLog')
                    ->whereIn('EXID', [120])
                    ->where('TASKFILE', '<>', 'C:\Users\Administrator\Desktop\方案\kdzw\kdzw_go.xml')
                    ->whereIn('RULEFILE', $rule)
                    ->get()
                    ->toArray();

                $taskLogs = array_merge($taskLogs, $taskLog);
            }

            $nids = array_column($taskLogs, 'NID');
            $articles = Article::select(['articleid', 'lastupdate'])->whereIn('articleid', $nids)->get()->keyBy('articleid')->toArray();
            foreach ($taskLogs as $log) {
                if (in_array($log->NID, $all_ids)) {
                    continue;
                }
                $all_ids[] = $log->NID;

                $last_update = isset($articles[$log->NID]) ? $articles[$log->NID]['lastupdate'] : time();
                if (!$is_all) {
                    if ($last_update >= $max_date) {
                        continue;
                    }
                    if ($last_update <= $min_date) {
                        continue;
                    }
                }

                $rule_ids[$rule_name]['origin'][$log->GETID] = date('Y-m-d H:i:s', $last_update);
                $rule_ids[$rule_name]['local'][] = $log->NID;
            }
        }

        foreach ($rule_ids as $rule_name => $rule) {
            $count = count(array_keys($rule['local']));
            echo $rule_name . "({$count}):\n";
            asort($rule['local']);
            echo implode(',', array_keys($rule['local'])) . "\n";
        }
    }
}
