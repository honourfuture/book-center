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

    public function read()
    {
        $files = $this->storage->allFiles("/");

        foreach ($files as $file) {
            $this->_read_file($file);
            $this->_insert_article();
            die;
        }
    }

    private function _insert_article()
    {
        echo $this->articleName."\n";
        echo $this->author."\n";
        echo $this->desc."\n";

        print_r($this->chapters);
    }

    private function _read_file($file)
    {
        $path = $this->storage->path($file);
        $file = fopen($path, 'r');
        $linuNumber = 0;
        while (!feof($file)) {
            $content = fgets($file);
            $linuNumber++;

            if ($this->_is_continue($content, $linuNumber)) {
                continue;
            }
            if ($linuNumber == 4) {
                $this->articleName = trim($content);
                continue;
            }


            if (strpos($content, '作者：') !== false) {
                $this->author = str_replace('作者：', '', trim($content));
                continue;
            }

            if (strpos($content, '内容简介：') !== false) {
                $this->textType = 'DESC';
                continue;
            }

            $pattern = "/^[ 　\t\n]/u";
            if (preg_match($pattern, $content)) {
                if ($this->textType == 'DESC') {
                    $this->desc .= $content;
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
