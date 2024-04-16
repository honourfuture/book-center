<?php

namespace App\Http\Controllers;

use App\Enums\RuleEnum;
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
}
