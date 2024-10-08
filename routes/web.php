<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/get-empty-article', [\App\Http\Controllers\SqliteController::class, 'get_empty_article']);
Route::get('/get-add', [\App\Http\Controllers\SqliteController::class, 'get_add']);

//获取120错误
Route::get('/get-tieshu-list', [\App\Http\Controllers\SqliteController::class, 'get_tieshu_list']);
Route::get('/get-meigui-list', [\App\Http\Controllers\SqliteController::class, 'get_source_list']);
Route::get('/get-mayi-list', [\App\Http\Controllers\SqliteController::class, 'get_mayi_list']);
Route::get('/get-tieshu-replace', [\App\Http\Controllers\SqliteController::class, 'get_auto_replace']);

//关键词
Route::get('/get-keyword', [\App\Http\Controllers\KeywordController::class, 'get_keyword']);

//远程访问
Route::get('/remote-site', [\App\Http\Controllers\RemoteSiteController::class, 'view']);
Route::get('/remote-site/cookie', [\App\Http\Controllers\RemoteSiteController::class, 'get_origin_view_cookie']);

//最近访问列表
//Route::get('/update-article', [\App\Http\Controllers\UpdateArticleController::class, 'article']);

//校对
Route::get('/article/{id}', [\App\Http\Controllers\ArticleController::class, 'article'])->name('check-error-article');
Route::any('/check-articles', [\App\Http\Controllers\ArticleController::class, 'check_articles']);
Route::get('/error-articles', [\App\Http\Controllers\ArticleController::class, 'error_articles']);

//手动校对
Route::get('/hand-articles', [\App\Http\Controllers\ArticleController::class, 'hand_articles']);

//精校
Route::get('/fl-article-chapter', [\App\Http\Controllers\ArticleController::class, 'chapter']);
Route::get('/fl-article-book', [\App\Http\Controllers\ArticleController::class, 'book']);
Route::get('/fl-article-list', [\App\Http\Controllers\ArticleController::class, 'list']);

//static
Route::get('/static', [\App\Http\Controllers\StaticController::class, 'build_static']);
Route::get('/update-article', [\App\Http\Controllers\StaticController::class, 'update_article']);
Route::get('/job-article', [\App\Http\Controllers\StaticController::class, 'job_article']);
Route::get('/hour-job-article', [\App\Http\Controllers\StaticController::class, 'hour_job_article']);
Route::get('/day-job-article', [\App\Http\Controllers\StaticController::class, 'day_job_article']);
Route::get('/add-article', [\App\Http\Controllers\StaticController::class, 'add_article']);
Route::get('/update-article-crontab', [\App\Http\Controllers\StaticController::class, 'update_article_crontab']);
//source
Route::get('/create-source/{id}', [\App\Http\Controllers\ArticleController::class, 'create_source_article'])->name('create-source');
Route::post('/do-create-source-article', [\App\Http\Controllers\ArticleController::class, 'do_create_source_article'])->name('do-create-source-article');

//绑定来源
Route::get('/create-sources', [\App\Http\Controllers\SearchSpiderController::class, 'create_sources'])->name('create-sources');
Route::post('/do-create-sources', [\App\Http\Controllers\SearchSpiderController::class, 'do_create_sources'])->name('do-create-sources');

//Tool
Route::view('/tool', 'tool');
Route::view('/tool/diff', 'diff');

//临时更新4k
Route::view('/4ksw', '4ksw');

//OCR
Route::get('/ocr', [\App\Http\Controllers\OCRController::class, 'do_ocr']);
//test
Route::get('/test', [\App\Http\Controllers\TestController::class, 'test']);

//nginx spider
Route::get('/search-spider', [\App\Http\Controllers\SearchSpiderController::class, 'spider_articles']);
Route::get('/get-artisan', [\App\Http\Controllers\SearchSpiderController::class, 'get_artisan']);
Route::get('/search-spider/{id}', [\App\Http\Controllers\SearchSpiderController::class, 'spider_article']);
Route::post('/search-spider/{id}', [\App\Http\Controllers\SearchSpiderController::class, 'set_article_peg']);
Route::get('/spider_statics', [\App\Http\Controllers\SearchSpiderController::class, 'spider_statics']);
Route::get('/do-low-article/{id}', [\App\Http\Controllers\SearchSpiderController::class, 'do_low_article']);
//loss
Route::get('/trend-article', [\App\Http\Controllers\SearchSpiderController::class, 'trend_article']);
//source
Route::get('/add-source-article', [\App\Http\Controllers\SourceArticleController::class, 'add_source_article']);

Route::get('/log-shell', [\App\Http\Controllers\ToolController::class, 'shell']);
Route::post('/do-log-shell', [\App\Http\Controllers\ToolController::class, 'do_shell']);

Route::get('/day-counter', [\App\Http\Controllers\AnalyseController::class, 'day']);
Route::get('/day-rule', [\App\Http\Controllers\AnalyseController::class, 'day_rule']);
Route::get('/day-show', [\App\Http\Controllers\AnalyseController::class, 'show']);
