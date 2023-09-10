<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 17:43
 */

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ExcellentArticleService
{
    private $articleName;
    private $author;
    private $desc;
    private $chapters;
    private $chapterName;
    private $textType;
    private $chapterNumber = 0;

    private $storage;

    public function __construct()
    {
        $this->storage = Storage::disk('excellent');
    }

    public function all()
    {
        $files = $this->storage->allFiles("/");

        $list = [];
        foreach ($files as $file) {
             $list[] = str_replace(['.json', '.txt'], '', $file);
        }

        return array_unique($list);
    }

    public function read($book_name = "妖魔复苏：开局觉醒神象镇狱劲")
    {
        if($this->storage->exists("{$book_name}.json")){
            return $this->storage->get("{$book_name}.json");
        }

        $this->_read_file($book_name . '.txt');

        $json = json_encode([
            'article_name' => $this->articleName,
            'author' => $this->author,
            'chapters' => $this->chapters
        ]);


        $this->storage->put("/{$book_name}.json", $json);

        return $json;
    }

    private function _insert_article()
    {
        echo $this->articleName . "\n";
        echo $this->author . "\n";
        echo $this->desc . "\n";

        print_r($this->chapters);
    }

    private function _read_file($file)
    {
        $path = $this->storage->path($file);
        $file = fopen($path, 'r');
        $linuNumber = 0;
        while (!feof($file)) {

            $content = fgets($file);
            if(!mb_check_encoding($content, 'UTF-8')){
                $content = iconv('gbk', 'utf-8//IGNORE', $content);
            }

            $linuNumber++;

            if ($this->_is_continue($content, $linuNumber)) {
                continue;
            }
            if ($linuNumber == 4) {
                $this->articleName = trim($content);
                continue;
            }

            if (strpos($content, '作者：') !== false && !$this->author) {
                $this->author = str_replace('作者：', '', trim($content));
                continue;
            }

            if (strpos($content, '内容简介：') !== false && !$this->desc) {
                $this->textType = 'DESC';
                continue;
            }

            $pattern = "/^[ 　\t\n]/u";
            if (preg_match($pattern, $content) || !strpos($content, '章')) {
                if ($this->textType == 'DESC') {
                    $this->desc .= str_replace('内容简介：', '', trim($content));;
                }

                if ($this->textType == 'CHAPTER') {
                    if (isset($this->chapters[$this->chapterNumber])) {
                        $this->chapters[$this->chapterNumber]['text'] .= $content;
                        continue;
                    }

                    $this->chapters[$this->chapterNumber] = ['chapterName' => $this->chapterName, 'text' => $content];
                }
            } else {
                $this->chapterNumber++;
                $this->chapterName = trim($content);
                $this->textType = 'CHAPTER';
            }
        }
        fclose($file);
    }


    private function _is_continue($text, $lineNum)
    {

        $is_continue = false;
        if (trim($text) == '') {

            $is_continue = true;
        }

        if (in_array($lineNum, [0, 1, 2, 3])) {
            $is_continue = true;
        }

        return $is_continue;
    }


}
