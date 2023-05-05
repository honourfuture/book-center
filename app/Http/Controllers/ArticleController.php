<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Facades\Storage;
use function Livewire\str;

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
            $chapter->error_message = [];

            $chapter_file_path = "{$short_id}/{$article->articleid}/$chapter->chapterid.txt";

            if (!$storage->exists($chapter_file_path)) {
                $chapter->error_message = ["txt丢失"];
                continue;
            }

            $chapter->size = $storage->size($chapter_file_path);
            $chapter_file = $storage->get($chapter_file_path);
            $chapter->content = iconv('gbk', 'utf-8//IGNORE', $chapter_file);
            $chapter->strlen = mb_strlen($chapter->content);

            $chapter->error_message = $this->_check_chapter($chapter->content, $chapter->strlen);
        }

        return view('chapter', ['article' => $article, 'chapters' => $chapters]);
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
        if (strpos($content, "小侯爷")) {
            $error_message[] = "小侯爷";
        }
        if (strpos($content, "是一个失败者")) {
            $error_message[] = "失败者";
        }
        if ($str_len < 450) {
            $error_message[] = "字数异常";
        }

        return $error_message;
    }

    public function create()
    {
        $storage = Storage::disk('excellent');

        $files = $storage->allFiles("/");

        foreach ($files as $file) {
            $path = $storage->path($file);
            $file = fopen($path, 'r');

            $line_num = 0;
            while (!feof($file)) {
                $line = fgets($file);
                $line_num++;
                if ($line_num < 4) {
                    continue;
                }
                $is_indented = 0;
                if (strpos('　　', $line)) {
                    $is_indented = 1;
                }

                echo "QQQQQQQ:".$is_indented."\n";
                var_dump($line);
                echo "\n";
                if($line_num == 500){
                    break;
                }
            }

            fclose($file);


        }
    }
}
