<?php

namespace App\Http\Controllers;

use App\Enums\QueueNameEnum;
use App\Jobs\AutoArticleAllJob;
use App\Jobs\CrontabUpdateArticleJob;
use App\Models\Article;
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

        return view('static/mayi', ['update_articles' => $update_articles]);
    }

    public function job_article(Request $request)
    {
        $site = $request->get('site', 'mayi');
        $type = $request->get('type', 1);
        $update_articles = BookUpdateArticle::where('type', $type)->where('site', $site)->where('is_push_job', 0)->get();
        $ids = $update_articles->pluck('id');

        $update_article_ids = $update_articles->pluck('local_article_id')->unique()->toArray();

        foreach ($update_article_ids as $article_id) {
            if ($article_id == 0) {
                continue;
            }
            dispatch((new AutoArticleAllJob($article_id))->onQueue(QueueNameEnum::UPDATE_ALL_JOB));
        }
        BookUpdateArticle::whereIn('id', $ids)->update(['is_push_job' => 1]);
        return view('static/mayi', ['update_articles' => $update_articles]);
    }

    public function add_article(Request $request)
    {
        $site = $request->get('site', 'mayi');
        $type = $request->get('type', 2);

        $source_articles = SourceArticle::where('local_article_id', 0)
            ->where('source', $site)
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get()->keyBy(function ($article) {
                return md5($article->article_name . '-' . $article->author);
            });

        $authors = $source_articles->pluck('author');
        $article_names = $source_articles->pluck('article_name');

        $articles = Article::select(['articleid', 'articlename', 'author'])->whereIn('articlename', $article_names)
            ->whereIn('author', $authors)->get()->keyBy(function ($article) {
                return md5($article->articlename . '-' . $article->author);
            })->toArray();


        $add_articles = [];
        foreach ($source_articles as $key => $source_article) {
            if (isset($articles[$key])) {
                SourceArticle::where('id', $source_article->id)
                    ->update(['local_article_id' => $articles[$key]['articleid']]);
                continue;
            }

            $source_article_id = $source_article['article_id'];
            $index = intval($source_article_id / 1000);
            $add_articles[] = [
                'article_url' => "https://www.mayiwsk.com/{$index}_{$source_article_id}/index.html",
                'article_name' => $source_article['article_name'],
                'author' => $source_article['author'],
                'last_chapter' => ''
            ];

        }

        $update_articles = BookUpdateArticle::select(['article_name', 'article_url', 'author', 'last_chapter'])
            ->where('type', $type)
            ->where('site', $site)
            ->where('local_article_id', 0)
            ->limit(100)
            ->get();

        $ids = $update_articles->pluck('id');
        $update_articles = $update_articles->toArray();

        $update_articles = array_merge($update_articles, $add_articles);

        BookUpdateArticle::whereIn('id', $ids)->delete();
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
