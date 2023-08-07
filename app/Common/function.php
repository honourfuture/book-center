<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/8/3
 * Time: 18:09
 */

/**
 * 清除非中文
 * @param $str
 * @return string|string[]|null
 */
function clear_text($str)
{
    $str = preg_replace('/第(?:\d+|[一二三四五六七八九十百千]+)章/u', '第章', $str);
    $str = str_replace([
        '求收藏',
        '求订阅',
        '求月票',
        '求首订',
        '求推荐',
        '求追读',
    ], '', $str);
    $str = preg_replace('/^.*?(?=第章)/s', '', $str);
    $pattern = '/[^\x{4e00}-\x{9fa5}]/u';
    $replacement = '';
    return preg_replace($pattern, $replacement, $str);
}

/**
 * 相似度对比
 * @param $str_1
 * @param $str_2
 */
function text_composer($str_1, $str_2)
{
    $str_len = strlen($str_1);
    $leven = levenshtein($str_1, $str_2);
    $similar = similar_text($str_1, $str_2);

    echo '$leven:' . $leven . "\n";
    echo '$similar:' . $similar . "\n";
    echo '$str_len:' . $str_len . "\n";
}

function remove_space($text){
    return preg_replace("/(\s|\&nbsp\;||\xc2\xa0)/","", $text);;
}

