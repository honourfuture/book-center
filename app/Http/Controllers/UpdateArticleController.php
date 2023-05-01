<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TargetArticle;
use App\Models\Article;
use App\Services\OverviewUrlService;
use Illuminate\Http\Request;

class UpdateArticleController extends Controller
{
    public function article(Request $request)
    {
        $start_date = $request->get('start_date', date('Y-m-d 00:00:00'));
        $end_date = $request->get('start_date', date('Y-m-d 23:59:59'));

        /** @var OverviewUrlService $overviewUrlService */
        $overviewUrlService = app('OverviewUrlService');
        $overviewUrlService->overview_urls('baidu', $start_date, $end_date);

        $article = Article::first();
    }
}
