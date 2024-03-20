<?php

namespace App\Http\Controllers;

use App\Enums\QueueNameEnum;
use App\Jobs\AutoArticleAllJob;
use App\Jobs\CrontabUpdateArticleJob;
use App\Models\BookUpdateArticle;
use App\Models\SourceArticle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class StaticController extends Controller
{
    public function build_static()
    {
        $i = 1;
        SourceArticle::where('source', '4ksw')
            ->orderBy('article_id', 'desc')
            ->whereNotIn('article_id', $this->_not_in_article_ids())
            ->where('desc', 'like', '%飞卢%')
            ->chunk(100, function ($source_articles) use (&$i) {
                $static = view('static/4ksw', ['source_articles' => $source_articles])->__toString();
                file_put_contents("4ksw/{$i}.html", $static);
                $i = $i + 1;
            });;
    }

    private function _not_in_article_ids()
    {
        return [];
    }

    public function update_article(Request $request)
    {
        $site = $request->get('site', 'mayi');
        $type = $request->get('type', 1);
        $update_articles = BookUpdateArticle::where('type', $type)->where('site', $site)->limit(200)->get();
        $ids = $update_articles->pluck('id');

        $update_article_ids = $update_articles->pluck('local_article_id')->unique()->toArray();

        foreach ($update_article_ids as $article_id) {
            if ($article_id == 0) {
                continue;
            }
            dispatch((new AutoArticleAllJob($article_id))->onQueue(QueueNameEnum::UPDATE_ALL_JOB));
        }
        BookUpdateArticle::whereIn('id', $ids)->delete();
        return view('static/mayi', ['update_articles' => $update_articles]);
    }

    public function add_article(Request $request)
    {
        $site = $request->get('site', 'mayi');
        $type = $request->get('type', 2);
        $update_articles = BookUpdateArticle::where('type', $type)
            ->where('site', $site)
            ->where('local_article_id', 0)
            ->limit(10)
            ->get();

        $ids = $update_articles->pluck('id');

//        BookUpdateArticle::whereIn('id', $ids)->delete();
        return view('static/mayi', ['update_articles' => $update_articles]);
    }

    public function update_article_crontab()
    {
        dispatch((new CrontabUpdateArticleJob())->onQueue(QueueNameEnum::UPDATE_ARTICLE_JOB));
        dispatch((new CrontabUpdateArticleJob())->onQueue(QueueNameEnum::UPDATE_ARTICLE_JOB))->delay(Carbon::now()->addSeconds(10));
        dispatch((new CrontabUpdateArticleJob())->onQueue(QueueNameEnum::UPDATE_ARTICLE_JOB))->delay(Carbon::now()->addSeconds(20));
        dispatch((new CrontabUpdateArticleJob())->onQueue(QueueNameEnum::UPDATE_ARTICLE_JOB))->delay(Carbon::now()->addSeconds(30));
        dispatch((new CrontabUpdateArticleJob())->onQueue(QueueNameEnum::UPDATE_ARTICLE_JOB))->delay(Carbon::now()->addSeconds(40));
        dispatch((new CrontabUpdateArticleJob())->onQueue(QueueNameEnum::UPDATE_ARTICLE_JOB))->delay(Carbon::now()->addSeconds(50));

    }
}
