<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 17:43
 */

namespace App\Services;

use App\Enums\ChapterEnum;
use App\Models\Chapter;
use Illuminate\Support\Facades\Storage;

class ErrorArticleService
{
    public function check_error_chapters($article)
    {
        $article_id = $article->articleid;

        $chapters = Chapter::select([
            'chapterid', 'articleid',
            'chaptername', 'lastupdate', 'chapterorder'
        ])->where('articleid', $article_id)
            ->where('chaptertype', 0)
            ->where('is_right', 0)
            ->orderBy('chapterorder', 'desc')
            ->get();

        $short_id = intval($article->articleid / 1000);

        $storage = Storage::disk('article');

        $all_chapters = collect([]);

        foreach ($chapters as $chapter) {
            $chapter_file_path = "{$short_id}/{$article->articleid}/$chapter->chapterid.txt";
            if (!$storage->exists($chapter_file_path)) {
                continue;
            }

            $all_chapter = $chapter;
            $chapter_file = $storage->get($chapter_file_path);
            $content = iconv('gbk', 'utf-8//IGNORE', $chapter_file);

            $all_chapter->file_path = $chapter_file_path;

            $is_no_check = $this->is_no_check_chapter_name($chapter->chaptername);
            if($is_no_check){
                $all_chapter->is_error_chapter = 0;
            }else{
                $all_chapter->is_error_chapter = $this->is_error_chapter($content);
            }

            $all_chapters[] = $all_chapter;
        }

        return $all_chapters;
    }

    public function is_no_check_chapter_name($text)
    {
        foreach (ChapterEnum::NoCheckChapterName as $name) {
            if($text == $name){
                return 1;
            }
        }

        foreach (ChapterEnum::NoCheckChapterNameSTROPS as $name) {
            if (strpos($text, $name) !== false) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * @param $content
     * @return int
     */
    public function is_error_chapter($content)
    {
        if (strpos($content, "正在手打") !== false) {
            return 1;
        }
        if (strpos($content, "灵魂契约，契合灵魂，只要自己不解除，哪怕对方手段通天") !== false) {
            return 1;
        }
        if (strpos($content, "小侯爷，您快点起来吧，轮到我们巡逻了") !== false) {
            return 1;
        }
        if (strpos($content, "我是一个失败者") !== false) {
            return 1;
        }
        if (strpos($content, "作为捕蛇者") !== false) {
            return 1;
        }
        if (strpos($content, "先祖在上") !== false) {
            return 1;
        }
        if (strpos($content, "也杀死了多位八阶强者") !== false) {
            return 1;
        }
        $str_len = mb_strlen($content);
        if ($str_len < 400) {
            return 1;
        }

        return 0;
    }
}
