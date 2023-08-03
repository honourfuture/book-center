<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Chapter;
use App\Services\ErrorArticleService;
use App\Services\SpiderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use QL\QueryList;

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
        $article = Article::find(62822);

        /** @var ErrorArticleService $error_article_service */
        $error_article_service = app('ErrorArticleService');
        $chapters = $error_article_service->check_error_chapters($article);

        $right_chapters = $chapters->where('is_error_chapter', 0);
        $right_chapter_ids = $right_chapters->pluck('chapterid')->toArray();
        $error_chapters = $chapters->where('is_error_chapter', 1)->first();

        if (!$error_chapters) {
            $this->info("NB. 当前书籍无错误章节");
            exit;
        }

        $origin_article = $this->_get_origin_article($article);
        $origin_article = isset($origin_article[0]) ? $origin_article[0] : [];

        $origin_chapters = [];
        foreach ($origin_article['chapters'] as $key => $origin_chapter) {
            if (!isset($origin_article['chapter_hrefs'][$key])) {
                $this->info('当前URL对数量不一致');
                exit;
            }
            $origin_chapter = clear_text($origin_chapter);
            $origin_chapters[$origin_chapter]['url'] = "https://www.mayiwxw.com" . $origin_article['chapter_hrefs'][$key];
            $origin_chapters[$origin_chapter]['original_chapter_name'] = $origin_article['chapter_hrefs'][$key];
        }

        $error_chapter_ids = [];
        $storage = Storage::disk('article');
        foreach ($chapters as $chapter) {
            if ($chapter->is_error_chapter == 0) {
                continue;
            }

            $chapter_name = clear_text($chapter->chaptername);

            if (isset($origin_chapters[$chapter_name])) {

                $url = $origin_chapters[$chapter_name]['url'];
                $text = $this->_get_origin_chapter($url);

                $is_error = $error_article_service->is_error_chapter($text);
                if (!$is_error) {
                    $storage->get($chapter->file_path);
                    $storage->put($chapter->file_path, $text);
                    $right_chapter_ids[] = $chapter->chapterid;
                    continue;
                }
            }

            $error_chapter_ids[] = $chapter->chapterid;
        }

        if ($right_chapter_ids) {
            $this->_set_right_chapters($right_chapter_ids);
        }

        if ($error_chapter_ids) {
            $this->_set_error_chapters($error_chapter_ids);
        }
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
        Chapter::whereIn('chapterid', $error_chapter_ids)->error_nums('votes', 1);;
    }
    
    /**
     * @param $article
     * @return array
     */
    private function _get_origin_article($article)
    {
        $url = $this->_get_origin_url($article);

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
        return 'https://www.mayiwxw.com/99_99388/index.html';
    }

}
