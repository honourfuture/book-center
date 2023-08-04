<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Chapter;
use App\Models\SourceArticle;
use App\Services\ErrorArticleService;
use App\Services\SpiderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixChapter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:chapter {--article_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix:chapter';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $article_id = $this->option('article_id');
        $article = Article::find($article_id);
        $this->line("[{$article_id}] 开始修复:  {$article->articlename}");

        $this->line("[{$article_id}] 开始检测错误章节: ");

        /** @var ErrorArticleService $error_article_service */
        $error_article_service = app('ErrorArticleService');
        $chapters = $error_article_service->check_error_chapters($article);

        $right_chapters = $chapters->where('is_error_chapter', 0);
        $right_chapter_ids = $right_chapters->pluck('chapterid')->toArray();
        $all_error_chapters = $chapters->where('is_error_chapter', 1);
        $this->line("[{$article_id}] 错误章节数: {$all_error_chapters->count()} ");

        if ($all_error_chapters->isEmpty()) {
            $this->info("[{$article_id}] 当前书籍无错误章节");
            exit;
        }

        $origin_article = $this->_get_origin_article($article);
        $origin_article = isset($origin_article[0]) ? $origin_article[0] : [];

        $origin_chapters = [];
        foreach ($origin_article['chapters'] as $key => $origin_chapter) {

            if (!isset($origin_article['chapter_hrefs'][$key])) {
                $this->error("[{$article_id}] 当前URL对数量不一致");
                exit;
            }

            $clear_origin_chapter = clear_text($origin_chapter);
            $origin_chapters[$clear_origin_chapter]['original_chapter_name'] = $origin_chapter;
            $origin_chapters[$clear_origin_chapter]['url'] = $origin_article['chapter_hrefs'][$key];
        }

        $this->line("[{$article_id}] 开始修复错误章节");

        $error_chapter_ids = [];
        $storage = Storage::disk('article');
        foreach ($chapters as $chapter) {
            if ($chapter->is_error_chapter == 0) {
                continue;
            }

            $chapter_name = clear_text($chapter->chaptername);

            if (isset($origin_chapters[$chapter_name])) {
                $url = $origin_chapters[$chapter_name]['url'];

                $this->line("[{$article_id}] 开始修复章节[{$chapter->chapterid}]: {$chapter->chaptername}");

                $text = $this->_get_origin_chapter($url);
                $is_error = $error_article_service->is_error_chapter($text);
                if (!$is_error) {
                    $storage->get($chapter->file_path);
                    $storage->put($chapter->file_path, $text);
                    $right_chapter_ids[] = $chapter->chapterid;
                    $this->info("[{$article_id}] 修复章节[{$chapter->chapterid}]: {$chapter->chaptername} 成功");

                    continue;
                }else{
                    $this->error("[{$article_id}] 修复章节[{$chapter->chapterid}]: {$chapter->chaptername} 失败, 源站章节错误");
                }
            }else{
                $this->error("[{$article_id}] 修复章节[{$chapter->chapterid}]: {$chapter->chaptername} 失败, 未找到章节");
            }

            $error_chapter_ids[] = $chapter->chapterid;
        }

        if ($right_chapter_ids) {
            $this->_set_right_chapters($right_chapter_ids);
        }

        if ($error_chapter_ids) {
            $this->_set_error_chapters($error_chapter_ids);
        }

        $this->line("[{$article_id}] 修复完成");
    }


    private function _set_right_chapters($right_chapter_ids)
    {
        Chapter::whereIn('chapterid', $right_chapter_ids)->update([
            'is_right' => 1,
            'lastupdate' => time()
        ]);
    }

    private function _set_error_chapters($error_chapter_ids)
    {
        Chapter::whereIn('chapterid', $error_chapter_ids)->increment('error_nums', 1);;
    }

    /**
     * @param $article
     * @return array
     */
    private function _get_origin_article($article)
    {
        $this->line("[{$article->articleid}] 获取远程站点");

        $url = $this->_get_origin_url($article);
        $this->line("[{$article->articleid}] 获取远程站点成功 [{$url}]");

        if(!$url){
            $this->error("[{$article->articleid}] 未找到远程url");
            exit;
        }

        /** @var SpiderService $spiderService */
        $spiderService = app('SpiderService');
        return $spiderService->get_article($url);
    }

    /**
     * @param $article
     * @return array
     */
    private function _get_origin_chapter($url)
    {
        /** @var SpiderService $spiderService */
        $spiderService = app('SpiderService');
        return $spiderService->get_chapter($url);
    }

    /**
     * @param $article
     * @return string
     */
    private function _get_origin_url($article)
    {
        $origin_articles = SourceArticle::where('article_name', $article->articlename)
            ->get();

        $origin_article = $origin_articles->where('author', $article->author)->first();
        /** @var SpiderService $spiderService */
        $spiderService = app('SpiderService');
        if ($origin_article) {
            return $spiderService->build_article_url($origin_article->article_id);
        }

        foreach ($origin_articles as $origin_article) {
            if ($origin_article->author) {
                continue;
            }

            $url = $spiderService->build_article_url($origin_article->article_id);

            $article_info = $spiderService->get_article_info($url);

            if ($article_info) {
                $article_info['author'] = remove_space($article_info['author']);
                $article_info['desc'] = remove_space($article_info['desc']);

                SourceArticle::where('article_id', $origin_article->article_id)->update([
                    'author' => $article_info['author'],
                    'desc' => $article_info['desc'],
                ]);
                if ($article_info['author'] == $article['author']) {
                    if (isset($article_info['desc']) && $article_info['desc']) {
                        Article::where('articleid', $article->articleid)->update([
                            'intro' => $article_info['desc']
                        ]);
                    }

                    return $spiderService->build_article_url($origin_article->article_id);
                }
            }
        }

        return false;
    }
}
