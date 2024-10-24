<?php

namespace App\Console\Commands;

use App\Exceptions\FixChapterException;
use App\Models\Article;
use App\Models\Chapter;
use App\Models\ChapterFix;
use App\Services\ErrorArticleService;
use App\Services\LoggerService;
use App\Services\SpiderService;
use App\Services\TextTypeSetService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FixChapter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:chapter {--article_id=} {--site=} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix:chapter';

    private $logger;

    private $spiderService;

    private $errorNums = 0;

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
        $this->errorNums = 0;
        $article_id = $this->option('article_id');
        $site = $this->option('site');
        $limit = $this->option('limit');
        if (!$limit) {
            $limit = 0;
        }
        //fuck 全错了
        $limit = 150;
        //fuck 全错了

        if (!$site) {
            $site = 'mayi';
        }

        /** @var SpiderService $spiderService */
        $this->spiderService = app('SpiderService', [
            'site' => $site,
        ]);

        /** @var LoggerService $loggerService */
        $loggerService = app('LoggerService');
        $this->logger = $loggerService->getLogger($site, 'fix_chapter');

        $article = Article::find($article_id);

        $this->_line_log("[{$article_id}] 开始修复:  {$article->articlename}");

        $this->_line_log("[{$article_id}] 开始检测错误章节: ");

        /** @var ErrorArticleService $error_article_service */
        $error_article_service = app('ErrorArticleService');
        $chapters = $error_article_service->check_error_chapters($article, $limit);

        $chapter_ids = $chapters->pluck('chapterid');
        $chapterFixes = ChapterFix::whereIn('chapter_id', $chapter_ids)->get();

        $right_chapters = $chapters->where('is_error_chapter', 0);
        $right_chapter_ids = $right_chapters->pluck('chapterid')->toArray();
        if ($right_chapter_ids) {
            $this->_set_right_chapters($right_chapter_ids, false);
        }

        $all_error_chapters = $chapters->where('is_error_chapter', 1);
        $this->_line_log("[{$article_id}] 错误章节数: {$all_error_chapters->count()} ");

        if ($all_error_chapters->count() > 500) {
            $this->_error_log("[{$article_id}] [10003] 错误章节过多");
//            throw new FixChapterException(400, '错误章节过多');
        }

        if ($all_error_chapters->isEmpty()) {
            $this->_info_log("[{$article_id}] 当前书籍无错误章节");
//            throw new FixChapterException(200, '当前书籍无错误章节');
        }

        $origin_article = $this->_get_origin_article($article);
        $origin_article = isset($origin_article[0]) ? $origin_article[0] : [];
        $origin_chapters = [];
        $full_origin_chapters = [];
        $repeat_chapter_count = 0;
        if (!$origin_article || !isset($origin_article['chapters'])) {
            throw new FixChapterException(400, '远程章节获取失败');
        }

        foreach ($origin_article['chapters'] as $key => $origin_chapter) {

            if (!isset($origin_article['chapter_hrefs'][$key])) {
                $this->_error_log("[{$article_id}] [10001] 当前URL对数量不一致");
                throw new FixChapterException(400, '当前URL对数量不一致');
            }

            $clear_origin_chapter = clear_text($origin_chapter);
            if (isset($origin_chapters[$clear_origin_chapter])) {
                $repeat_chapter_count++;
                continue;
            }
            $origin_chapters[$clear_origin_chapter]['original_chapter_name'] = $origin_chapter;
            $origin_chapters[$clear_origin_chapter]['url'] = $origin_article['chapter_hrefs'][$key];

            $full_origin_chapters[$origin_chapter]['original_chapter_name'] = $origin_chapter;
            $full_origin_chapters[$origin_chapter]['url'] = $origin_article['chapter_hrefs'][$key];
        }

        if ($repeat_chapter_count > 60) {
            $this->_error_log("[{$article_id}] [10002] 重复章节过多中止");
            throw new FixChapterException(400, '重复章节过多中止');
        }

        $this->_line_log("[{$article_id}] 开始修复错误章节");

        /** @var TextTypeSetService $textTypeSetService */
        $textTypeSetService = app('TextTypeSetService');

        $error_chapter_ids = $change_chapter_ids = [];
        $storage = Storage::disk('article');


        foreach ($chapters as $chapter) {
            $chapterFixContents = $chapterFixes->where('chapter_id', $chapter->chapterid)->pluck(['md5_content'])->toArray();
            if ($chapterFixContents && in_array($chapter->md5_content, $chapterFixContents)) {
                $this->_info_log("[{$article_id}] [{$chapter->chapterid} - {$chapter->chaptername}] md5验证无需修复");
                continue;
            }

            if ($chapter->is_error_chapter == 0) {
                $this->_info_log("[{$article_id}] [{$chapter->chapterid} - {$chapter->chaptername}] 章节名忽略无需修复");
                continue;
            }

            if ($chapter->error_nums > 100) {
                $this->_error_log("[{$article_id}] [10003] [{$chapter->chapterid} - {$chapter->chaptername}]错误次数过多跳出");
//                continue;
            }

            if ($this->errorNums > 8) {
                $this->_error_log("[{$article_id}] [10004] [{$chapter->chapterid} - {$chapter->chaptername}]连续错误次数过多跳出");
//                break;
            }
            $chapter_name = clear_text($chapter->chaptername);

            if (isset($full_origin_chapters[$chapter_name])) {
                $url = $full_origin_chapters[$chapter_name]['url'];
                $url = str_replace('http://m.', 'http://www.', $url);
                $this->_line_log("[{$article_id}] 开始修复章节[{$chapter->chapterid}]: {$chapter->chaptername}");

                $text = $this->_get_origin_chapter($url);

                $is_error = $error_article_service->is_error_chapter($text);
                if (!$is_error) {
                    $storage->get($chapter->file_path);
                    $text = iconv('utf-8', 'gbk//IGNORE', $text);
                    $text = $textTypeSetService->doTypeSet($text);
                    $md5_content = md5($text);
                    $storage->put($chapter->file_path, $text);
                    $change_chapter_ids[] = $chapter->chapterid;
                    $this->_info_log("[{$article_id}] 修复章节[{$chapter->chapterid}]: {$chapter->chaptername} 成功");
                    $this->errorNums = 0;
                    ChapterFix::updateOrCreate([
                        'chapter_id' => $chapter->chapterid,
                        'site' => $site,
                    ], [
                        'md5_content' => $md5_content,
                    ]);
                    continue;
                } else {
                    $this->errorNums++;
                    $this->_error_log("[{$article_id}] 修复章节[{$chapter->chapterid}]: {$chapter->chaptername} 失败, 源站章节错误[{}]");
                    continue;
                }

            }

            if (isset($origin_chapters[$chapter_name])) {
                $url = $origin_chapters[$chapter_name]['url'];

                $url = str_replace('http://m.', 'http://www.', $url);
                $this->_line_log("[{$article_id}] 开始修复章节[{$chapter->chapterid}]: {$chapter->chaptername}");

                $text = $this->_get_origin_chapter($url);
                $is_error = $error_article_service->is_error_chapter($text);
                if (!$is_error) {
                    $storage->get($chapter->file_path);
                    $text = iconv('utf-8', 'gbk//IGNORE', $text);
                    $text = $textTypeSetService->doTypeSet($text);
                    $md5_content = md5($text);

                    $storage->put($chapter->file_path, $text);
                    $change_chapter_ids[] = $chapter->chapterid;
                    $this->_info_log("[{$article_id}] 修复章节[{$chapter->chapterid}]: {$chapter->chaptername} 成功");
                    $this->errorNums = 0;
                    ChapterFix::updateOrCreate([
                        'chapter_id' => $chapter->chapterid,
                        'site' => $site,
                    ], [
                        'md5_content' => $md5_content,
                    ]);
                    continue;
                } else {
                    $this->errorNums++;
                    $this->_error_log("[{$article_id}] 修复章节[{$chapter->chapterid}]: {$chapter->chaptername} 失败, 源站章节错误");
                }
            } else {
                $this->_error_log("[{$article_id}] 修复章节[{$chapter->chapterid}]: {$chapter->chaptername} 失败, 未找到章节");
            }

            $error_chapter_ids[] = $chapter->chapterid;
        }

        if ($change_chapter_ids) {
            $this->_set_right_chapters($change_chapter_ids, true);
        }

        if ($error_chapter_ids) {
            $this->_set_error_chapters($error_chapter_ids);
        }

        $this->_line_log("[{$article_id}] 修复完成");
    }


    private function _set_right_chapters($right_chapter_ids, $is_update_time = false)
    {
        $update = [
            'is_right' => 1,
        ];

        if ($is_update_time) {
            $update['lastupdate'] = time();
        }

        Chapter::whereIn('chapterid', $right_chapter_ids)->update($update);
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
        $this->_line_log("[{$article->articleid}] 获取远程站点");

        $url = $this->spiderService->get_origin_url($article);
        if (!$url) {
            $this->_error_log("[{$article->articleid}] 未找到远程url");
            throw new FixChapterException(400, '未找到远程url');
        }

        $this->_line_log("[{$article->articleid}] 获取远程站点成功 [{$url}]");

        return $this->spiderService->get_article($url);
    }

    /**
     * @param $article
     * @return array
     */
    private function _get_origin_chapter($url)
    {
        return $this->spiderService->get_chapter($url);
    }

    private function _info_log($message, $verbosity = null)
    {
        $this->logger->info($message, $verbosity ? $verbosity : []);
        $this->info($message, $verbosity);
    }

    private function _line_log($message, $style = null, $verbosity = null)
    {
        $this->logger->info($message, $verbosity ? $verbosity : []);

        $this->line($message, $style, $verbosity);

    }

    private function _error_log($message, $verbosity = null)
    {
        $this->logger->error($message, $verbosity ? $verbosity : []);
        $this->error($message, $verbosity);
    }
}
