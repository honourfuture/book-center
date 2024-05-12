<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Chapter;
use App\Services\ErrorArticleService;
use App\Services\LoggerService;
use App\Services\SitemapService;
use App\Services\TextTypeSetService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ReplaceErrorChapter extends Command
{
    private $text_template = "
       ---chapter_name--

    《---article_name--》---chapter_name--

    正在手打中，请稍等片刻，内容更新后，请重新刷新页面，即可获取最新更新！
    ";
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'replace:ErrorChapter {--max_chapter_id=} {--min_chapter_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '替换章节错误章节';

    private $logger;


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
        /** @var LoggerService $loggerService */
        $loggerService = app('LoggerService');
        $this->logger = $loggerService->getLogger('replace', 'replace_chapter');

        $max_chapter_id = $this->option('max_chapter_id');
        $min_chapter_id = $this->option('min_chapter_id');
        $storage = Storage::disk('article');
        $storage_update = Storage::disk('article');

        Chapter::select(['chapterid', 'articleid', 'error_nums', 'articlename', 'chaptername', 'lastupdate', 'chapterorder'])
            ->orderBy('postdate', 'desc')->where('chapterid', '>', $min_chapter_id)
            ->where('chapterid', '<', $max_chapter_id)
            ->chunk(2000, function ($chapters) use ($storage, $storage_update) {

                /** @var TextTypeSetService $textTypeSetService */
                $textTypeSetService = app('TextTypeSetService');

                /** @var ErrorArticleService $error_article_service */
                $error_article_service = app('ErrorArticleService');
                foreach ($chapters as $chapter) {
                    $this->_info_log("[{$chapter->articleid} - {$chapter->chapterid}]: 检测 {$chapter->chaptername} ");

                    $short_id = intval($chapter->articleid / 1000);

                    $chapter_file_path = "{$short_id}/{$chapter->articleid}/$chapter->chapterid.txt";
                    if (!$storage->exists($chapter_file_path)) {
                        continue;
                    }
                    $chapter_file = $storage->get($chapter_file_path);
                    try {
                        $content = iconv('gbk', 'utf-8//IGNORE', $chapter_file);
                    } catch (\Exception $e) {
                        $content = mb_convert_encoding($chapter_file, 'utf-8', 'GBK');
                    }

                    $is_error_chapter = $error_article_service->is_special_error_chapter($content);

                    if ($is_error_chapter) {
                        $this->_error_log("[{$chapter->articleid} - {$chapter->chapterid}]: 错误 {$chapter->chaptername} ");
                        $text = $this->_build_template($chapter->articlename, $chapter->chaptername);
                        $storage_update->get($chapter_file_path);
                        $text = iconv('utf-8', 'gbk//IGNORE', $text);
                        $text = $textTypeSetService->doTypeSet($text);
                        $storage_update->put($chapter_file_path, $text);
                        $this->_info_log("[{$chapter->articleid} - {$chapter->chapterid}]: 修复完成 {$chapter->chaptername} ");
                    }
                }

            });
    }

    private function _info_log($message, $verbosity = null)
    {
        $this->logger->info($message, $verbosity ? $verbosity : []);
        $this->info($message, $verbosity);
    }

    private function _error_log($message, $verbosity = null)
    {
        $this->logger->error($message, $verbosity ? $verbosity : []);
        $this->error($message, $verbosity);
    }

    private function _build_template($article_name, $chapter_name)
    {
        $text = $this->text_template;
        $text = str_replace("---chapter_name--", $chapter_name, $text);
        return str_replace("---article_name--", $article_name, $text);
    }
}
