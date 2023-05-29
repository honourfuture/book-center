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

//远程访问
Route::get('/remote-site', [\App\Http\Controllers\RemoteSiteController::class, 'view']);

//最近访问列表
Route::get('/update-article', [\App\Http\Controllers\UpdateArticleController::class, 'article']);

//校对
Route::get('/article/{id}', [\App\Http\Controllers\ArticleController::class, 'article']);
Route::any('/check-articles', [\App\Http\Controllers\ArticleController::class, 'check_articles']);
Route::get('/error-articles', [\App\Http\Controllers\ArticleController::class, 'error_articles']);

//精校
Route::get('/article-create', [\App\Http\Controllers\ArticleController::class, 'create']);

//Tool
Route::view('/tool', 'tool');

//临时更新4k
Route::view('/4ksw', '4ksw');
