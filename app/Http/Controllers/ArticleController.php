<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;

class ArticleController extends Controller
{
    public function article($id, Request $request)
    {
        $page_size = $request->get('chapter_num', 50);

        $article = Article::find($id);

        $chapters = Chapter::where('articleid', $id)->orderBy('chapterorder', 'desc')->paginate($page_size);

        $short_id = intval($article->articleid / 1000);

        $storage = Storage::disk('article');

        foreach ($chapters as &$chapter) {
            $chapter->exists = false;
            $chapter_file_path = "{$short_id}/{$article->articleid}/$chapter->chapterid.txt";

            if (!$storage->exists($chapter_file_path)) {
                $chapter->exists = true;
                continue;
            }

            $chapter->size = $storage->size($chapter_file_path);
            $chapter_file = $storage->get($chapter_file_path);
            $chapter->content = iconv('gbk', 'utf-8//IGNORE', $chapter_file);

            $chapter->strlen = mb_strlen($chapter->content);

            try {
                $this->_check_chapter($chapter->content, $chapter->strlen);
            } catch (\Exception $exception) {
                $chapter->error_message = $exception->getMessage();
            }
        }
        return view('chapter', ['chapters' => $chapters]);
    }

    private function _check_chapter($content, $strlen)
    {
        if (strpos("正在手打", $content) || strpos("灵魂契约", $content)) {
            throw new \Exception('章节可能错误.', 400);
        }

        if ($strlen < 450) {
            throw new \Exception('章节长度异常', 400);
        }
    }
}
