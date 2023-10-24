<?php

namespace App\Http\Controllers;

use App\Enums\SourceEnum;
use App\Models\Chapter;
use App\Models\ErrorChapter;
use App\Models\HandArticle;
use App\Models\NginxAccessLog;
use App\Models\SourceArticle;
use App\Services\BaiduTjService;
use App\Services\ExcellentArticleService;
use App\Services\SpiderService;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use function Livewire\str;

class ArticleController extends Controller
{
    public function hand_articles(Request $request)
    {
        $articles = HandArticle::orderBy('order', 'desc')->get();
        return view('hand-article', ['articles' => $articles]);
    }

    public function article($id, Request $request)
    {
        $page_size = $request->get('chapter_num', 50);

        $article = Article::find($id);

        $chapters = Chapter::select([
            'chapterid', 'articleid',
            'chaptername', 'lastupdate', 'chapterorder', 'chaptertype'
        ])->where('articleid', $id)
            ->orderBy('chapterorder', 'desc')
            ->paginate($page_size);

        $short_id = intval($article->articleid / 1000);

        $storage = Storage::disk('article');

        foreach ($chapters as &$chapter) {
            $chapter->error_message = [];

            $chapter_file_path = "{$short_id}/{$article->articleid}/$chapter->chapterid.txt";

            if (!$storage->exists($chapter_file_path)) {
                $chapter->error_message = ["txt丢失"];
                continue;
            }

            $chapter->size = $storage->size($chapter_file_path);
            $chapter_file = $storage->get($chapter_file_path);

            $chapter->content = @iconv('gbk', 'utf-8//IGNORE', $chapter_file);


            $chapter->strlen = mb_strlen($chapter->content);

            $chapter->error_message = $this->_check_chapter($chapter->content, $chapter->strlen);
        }

        $source_articles = SourceArticle::where('article_name', $article->articlename)->where('author', $article->author)->get();
        return view('chapter', ['article' => $article, 'chapters' => $chapters, 'source_articles' => $source_articles]);
    }

    public function create_source_article($article_id)
    {
        $article = Article::find($article_id);
        $sources = SourceEnum::SOURCES;;

        return view('create-source', ['sources' => $sources, 'article' => $article]);
    }

    public function do_create_source_article(Request $request)
    {
        $article_id = $request->get('article_id');
        $author = $request->get('author');
        $source = $request->get('source');
        $article_name = $request->get('article_name');
        $origin_url = $request->get('origin_url');

        SourceArticle::create([
            'article_id' => $article_id,
            'author' => $author,
            'source' => $source,
            'article_name' => $article_name,
            'origin_url' => $origin_url,
        ]);

    }

    private function _check_chapter($content, $str_len)
    {
        $error_message = [];
        if (strpos($content, "正在手打")) {
            $error_message[] = "正在手打";
        }
        if (strpos($content, "灵魂契约")) {
            $error_message[] = "灵魂契约";
        }
        if (strpos($content, "夜的命名术作者")) {
            $error_message[] = "夜的命名术";
        }
        if (strpos($content, "小侯爷")) {
            $error_message[] = "小侯爷";
        }
        if (strpos($content, "是一个失败者")) {
            $error_message[] = "失败者";
        }
        if (strpos($content, "普通的小红狐")) {
            $error_message[] = "小红狐";
        }
        if (strpos($content, "作为捕蛇者")) {
            $error_message[] = "作为捕蛇者";
        }
        if (strpos($content, "先祖在上")) {
            $error_message[] = "先祖在上";
        }
        if (strpos($content, "也杀死了多位八阶强者")) {
            $error_message[] = "浩瀚宇宙";
        }
        if (strpos($content, "夜的命名术作者")) {
            $error_message[] = "夜的命名术";
        }
        if ($str_len < 666) {
            $error_message[] = "字数异常";
        }

        return $error_message;
    }


    public function list()
    {
        /** @var ExcellentArticleService $excellentArticleService */
        $excellentArticleService = app('ExcellentArticleService');
        echo "<ul>\n";
        $articles = $excellentArticleService->all();
        foreach ($articles as $article) {
            echo "<li><a href=\"/fl-article-book?book_name=$article\">{$article}</a></li>\n";
        }
        echo "</ul>";
    }

    public function book(Request $request)
    {
        $book_name = $request->get('book_name');

        /** @var ExcellentArticleService $excellentArticleService */
        $excellentArticleService = app('ExcellentArticleService');

        $info = $excellentArticleService->read($book_name);

        $info = json_decode($info, true);
        echo "<p articlename>{$info['article_name']}</p>";
        echo "<p author>{$info['author']}</p>";
        echo "<p desc>desc</p>";
        echo "<p category>category</p>";
        echo "<ul>\n";
        foreach ($info['chapters'] as $key => $chapter) {
            echo "<li><a href=\"{$key}\">{$chapter['chapterName']}</a></li>\n";
        }
        echo "</ul>";
    }

    public function chapter(Request $request)
    {
        $id = $request->get('chapter_id');
        $book_name = $request->get('book_name');

        /** @var ExcellentArticleService $excellentArticleService */
        $excellentArticleService = app('ExcellentArticleService');

        $info = $excellentArticleService->read($book_name);
        $info = json_decode($info, true);
        echo "<p id=\"content\">{$info['chapters'][$id]['text']}</p>";
    }

    public function check_articles(Request $request)
    {
        $article_ids = $request->get('article_ids');
        $article_ids = explode(',', $article_ids);

        $articles = Article::whereIn('articleid', $article_ids)->get();

        foreach ($articles as $article) {
            Chapter::select([
                'chapterid', 'articleid',
                'chaptername', 'lastupdate', 'chapterorder'
            ])->where('articleid', $article->articleid)
                ->orderBy('chapterorder', 'desc')
                ->chunk(50, function ($chapters) use ($article) {
                    $chapters = $this->_check_chapters($chapters, $article);
                    ErrorChapter::insert($chapters);
                });
        }
    }

    private function _check_chapters($chapters, $article)
    {
        $short_id = intval($article->articleid / 1000);

        $storage = Storage::disk('article');

        $error_chapters = [];
        foreach ($chapters as &$chapter) {
            $chapter->error_message = [];

            $chapter_file_path = "{$short_id}/{$article->articleid}/$chapter->chapterid.txt";

            if (!$storage->exists($chapter_file_path)) {
                $chapter->error_message = ["txt丢失"];
                continue;
            }

            $chapter->size = $storage->size($chapter_file_path);
            $chapter_file = $storage->get($chapter_file_path);
            $chapter->content = iconv('gbk', 'utf-8//IGNORE', $chapter_file);
//            $chapter->content = $chapter_file_path;
            $chapter->strlen = mb_strlen($chapter->content);
            $error_message = $this->_check_chapter($chapter->content, $chapter->strlen);

            if ($error_message) {
                $chapter->error_message = implode(',', $error_message);
                $error_chapter = $chapter->toArray();
                $error_chapter['created_at'] = date('Y-m-d H:i:s');
                $error_chapter['updated_at'] = date('Y-m-d H:i:s');
                $error_chapters[] = $error_chapter;
            }

        }

        return $error_chapters;
    }

    public function error_articles(Request $request)
    {
        $article_ids = $request->get('article_ids');
        $article_ids = explode(',', $article_ids);

        $articles = Article::whereIn('articleid', $article_ids)->with(['error_chapters'])->get();

        return view('error-chapter', ['articles' => $articles]);
    }
}
