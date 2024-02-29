<?php

namespace App\Console\Commands;

use App\Exceptions\FixChapterException;
use App\Models\Article;
use App\Models\Chapter;
use App\Services\ErrorArticleService;
use App\Services\LoggerService;
use App\Services\SpiderService;
use App\Services\TextTypeSetService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FixChapterName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:chapterName {--article_id=} {--site=} {--limit=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'fix:chapterName';

    private $logger;

    private $spiderService;

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
        $site = $this->option('site');
        $limit = $this->option('limit');
        if (!$limit) {
            $limit = 30;
        }
        if (!$site) {
            $site = 'mayi';
        }
        /** @var SpiderService $spiderService */
        $this->spiderService = app('SpiderService', [
            'site' => $site,
        ]);

        /** @var LoggerService $loggerService */
        $loggerService = app('LoggerService');
        $this->logger = $loggerService->getLogger($site, 'fix_chapter_name');

        $article = Article::find($article_id);

        $this->_line_log("[{$article_id}] 开始修复:  {$article->articlename}");

        $chapters = Chapter::where('articleid', $article_id)->orderBy('chapterid', 'desc')->limit($limit)->get()->toArray();
        $chapters = array_reverse($chapters);
        $this->_line_log("[{$article_id}] 获取远程章节: {$article->articlename}");
        $origin_article = $this->_get_origin_article($article);
        $origin_article = isset($origin_article[0]) ? $origin_article[0] : [];

        $right_search_key = 0;
        $error_pairs = [];
        $is_fix = 1;

        foreach ($chapters as $chapter) {
            if ($chapter['chaptername'] == '正文' || $chapter['chaptername'] == '全部章节') {
                continue;
            }

            $search_key = array_search($chapter['chaptername'], $origin_article['chapters']);
            if ($search_key) {
                $right_search_key = $search_key;
            } else {
                if (!isset($origin_article['chapters'][$right_search_key])) {
                    $is_fix = 0;
                    $this->_error_log("[{$chapter['articleid']}] [{$chapter['chapterid']}] [{$chapter['chaptername']}] 未找到对应章节");
                    break;
                }
                $right_search_key++;
                $error_pair = [
                    'local_chapter_id' => $chapter['chapterid'],
                    'local_chapter_name' => $chapter['chaptername'],
                    'origin_chapter_name' => $origin_article['chapters'][$right_search_key],
                    'origin_chapter_url' => $origin_article['chapter_hrefs'][$right_search_key],
                    'chapter' => $chapter
                ];
                $error_pairs[] = $error_pair;
            }
        }

        if (!$is_fix) {
            $this->_line_log("[{$article_id}] 无需修复");
            exit;
        }

        if(count(count($error_pair) > 20)){
            $this->error("[{$article_id}] 错误章节过多跳出!");
            exit;
        }

        Article::where('articleid', $article_id)->update(['lastupdate' => time()]);
        $short_id = intval($article->articleid / 1000);
        $storage = Storage::disk('article');
        /** @var ErrorArticleService $error_article_service */
        $error_article_service = app('ErrorArticleService');
        /** @var TextTypeSetService $textTypeSetService */
        $textTypeSetService = app('TextTypeSetService');

        foreach ($error_pairs as $error_pair) {

            $chapter = $error_pair['chapter'];
            $chapter_file_path = "{$short_id}/{$chapter['articleid']}/{$chapter['chapterid']}.txt";
            $this->_line_log("[{$article_id}] 开始修复章节[{$chapter['chapterid']}]: {$chapter['chaptername']}");
            $chapter_file = $storage->get($chapter_file_path);
            try {
                $local_text = iconv('gbk', 'utf-8//IGNORE', $chapter_file);
            } catch (\Exception $e) {
                $local_text = mb_convert_encoding($chapter_file, 'utf-8', 'GBK');
            }
            $local_is_error = $error_article_service->is_error_chapter($local_text);

            if ($local_is_error) {
                $this->_info_log("[{$article_id}] [{$chapter['chapterid']}] [{$chapter['chaptername']}] 文章内容无错误");
            } else {
                $text = $this->_get_origin_chapter($error_pair['origin_chapter_url']);
                $text = iconv('utf-8', 'gbk//IGNORE', $text);
                $text = $textTypeSetService->doTypeSet($text);
                $storage->put($chapter_file_path, $text);
                $this->_info_log("[{$article_id}] 修复章节[{$chapter['chapterid']}] [{$chapter['chaptername']}] 内容成功");
            }

            Chapter::where('chapterid', $error_pair['local_chapter_id'])->update([
                'chaptername' => $error_pair['origin_chapter_name'],
                'lastupdate' => time()
            ]);
            $this->_info_log("[{$article_id}] 修复章节[{$chapter['chapterid']}] [{$chapter['chaptername']} => {$error_pair['origin_chapter_name']}]  章节名成功");

        }
        $this->_line_log("[{$article_id}] 修复完成");
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
