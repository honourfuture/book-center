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
