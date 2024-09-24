<?php

namespace App\Http\Controllers;

use App\Enums\RuleEnum;
use App\Enums\SqliteErrorEnum;
use App\Models\Chapter;
use App\Models\Counter;
use App\Models\Domain;
use App\Models\ErrorChapter;
use App\Models\HandArticle;
use App\Services\ExcellentArticleService;
use App\Services\HttpProxyService;
use App\Services\SpiderService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use QL\QueryList;
use function Livewire\str;

class AnalyseController extends Controller
{
    public function day()
    {
        $last_update = strtotime(date('Y-m-d 23:59:59', strtotime('-1 day')));
        $next_update = strtotime(date('Y-m-d', strtotime('+1 day')));

        $article_count = Article::where('lastupdate', '>', $last_update)
            ->where('lastupdate', '<', $next_update)->count();

        $date = date('Y-m-d');

        Counter::updateOrCreate(
            ['date' => $date],
            ['day_update_counter' => $article_count]
        );
    }

    public function day_rule(Request $request)
    {
        $sqlite = $request->get('db_name', date('Ymd'));
        $sqlites = explode(',', $sqlite);

        foreach ($sqlites as $sqlite) {
            $date = date('Y-m-d', strtotime($sqlite));
            $taskLog = DB::connection($sqlite)
                ->select("SELECT RULEFILE,EXID,COUNT(1) AS COUNT FROM taskLog GROUP BY RULEFILE,EXID");

            $taskLog = json_encode($taskLog);
            Counter::updateOrCreate(
                ['date' => $date],
                ['rule_counter' => $taskLog]
            );
        }
    }

    public function show(Request $request)
    {
        $error_codes = $request->get('error_codes', '');
        if ($error_codes) {
            $error_codes = explode(',', $error_codes);
        }

        $counters = Counter::orderBy('date', 'desc')->get()->keyBy('date')->toArray();

        $rule_errors = [];
        $sqlite_error_codes = SqliteErrorEnum::ERROR_CODE;
        foreach ($counters as $key => $counter) {

            $rule_counters = json_decode($counter['rule_counter'], true);
            foreach ($rule_counters as $k => $rule_counter) {
                if ($error_codes) {
                    if (!in_array($rule_counter['EXID'], $error_codes)) {
                        unset($counters[$key]['rule_counters'][$k]);
                        continue;
                    }
                }
                $unique_key = md5($rule_counter['RULEFILE'] . '_' . $rule_counter['EXID']);
                $counters[$key]['rule_counters'][$unique_key]['exid_lang'] = $sqlite_error_codes[$rule_counter['EXID']];
                $counters[$key]['rule_counters'][$unique_key]['unique'] = $unique_key;
                $counters[$key]['rule_counters'][$unique_key]['count'] = $rule_counter['COUNT'];
                $counters[$key]['rule_counters'][$unique_key]['rule'] = str_replace('Rules\\', '', $rule_counter['RULEFILE']);
                $rule_errors[$unique_key]['rule'] = str_replace('Rules\\', '', $rule_counter['RULEFILE']);
                $rule_errors[$unique_key]['exid_lang'] = $sqlite_error_codes[$rule_counter['EXID']];
            }
        }
        return view('analyse.counter', [
            'rule_errors' => $rule_errors,
            'counters' => $counters
        ]);
    }
}
