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
    public function check_error_chapters($article, $limit = 0)
    {
        $article_id = $article->articleid;

        $chapters = Chapter::select([
            'chapterid', 'articleid', 'error_nums',
            'chaptername', 'lastupdate', 'chapterorder'
        ])->where('articleid', $article_id)
            ->where('chaptertype', 0)
//            ->where('is_right', 0)
            ->orderBy('chapterorder', 'desc');

        if ($limit) {
            $chapters = $chapters->limit($limit);
        }
        $chapters = $chapters->get();


        $short_id = intval($article->articleid / 1000);

        $storage = Storage::disk('article');

        $all_chapters = collect([]);

        foreach ($chapters as $chapter) {
            $chapter_file_path = "{$short_id}/{$article->articleid}/$chapter->chapterid.txt";
            if (!$storage->exists($chapter_file_path)) {
                echo(1);
                continue;
            }

            $all_chapter = $chapter;

            $chapter_file = $storage->get($chapter_file_path);
            $all_chapter->md5_content = md5($chapter_file);

            try {
                $content = iconv('gbk', 'utf-8//IGNORE', $chapter_file);
            } catch (\Exception $e) {
                $content = mb_convert_encoding($chapter_file, 'utf-8', 'GBK');
            }

            $all_chapter->file_path = $chapter_file_path;
            //fuck 全错了
            $is_no_check = $this->is_no_check_chapter_name($chapter->chaptername);
            if ($is_no_check || !$content) {
                $all_chapter->is_error_chapter = 0;
            } else {
                $all_chapter->is_error_chapter = 1;
            }
            $all_chapters[] = $all_chapter;
            continue;
            //fuck 全错了

            $is_no_check = $this->is_no_check_chapter_name($chapter->chaptername);
            if ($is_no_check || !$content) {
                $all_chapter->is_error_chapter = 0;
            } else {
                $all_chapter->is_error_chapter = $this->is_error_chapter($content);
            }


        }

        return $all_chapters;
    }

    public function is_no_check_chapter_name($text)
    {
        foreach (ChapterEnum::NoCheckChapterName as $name) {
            if ($text == $name) {
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
        if (strpos($content, "灵魂契约") !== false) {
            return 1;
        }
        if (strpos($content, "夜的命名术作者") !== false) {
            return 1;
        }
        if (strpos($content, "小侯爷，您快点起来吧，轮到我们巡逻了") !== false) {
            return 1;
        }
        if (strpos($content, "我是一个失败者") !== false) {
            return 1;
        }
        if (strpos($content, "普通的小红狐") !== false) {
            return 1;
        }
        if (strpos($content, "作为捕蛇者") !== false) {
            return 1;
        }
        if (strpos($content, "亲爱的访客") !== false) {
            return 1;
        }
        if (strpos($content, "先祖在上") !== false) {
            return 1;
        }
        if (strpos($content, "也杀死了多位八阶强者") !== false) {
            return 1;
        }
        if (strpos($content, "LqOFWfg2cmn") !== false) {
            return 1;
        }
        $str_len = mb_strlen($content);
        if ($str_len < 666) {
            return 1;
        }

        return 0;
    }

    public function is_special_error_chapter($content)
    {
        if (strpos($content, "LqOFWfg2cmn") !== false) {
            return 1;
        }
        $str_len = mb_strlen($content);
        if ($str_len < 666) {
            return 1;
        }

        return 0;
    }
}
