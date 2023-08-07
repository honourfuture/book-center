<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 23:37
 */

namespace App\Enums;

class ChapterEnum
{
    //无需检测章节名
    const NoCheckChapterNameSTROPS = [
        '请假',
        '病假',
        '请个假',
        '请一天假',
        '鸽一天',
        '请个小假'
    ];
    //无需检测章节名
    const NoCheckChapterName = [
        '上架感言',
        '求月票',
        '该章节已被锁定',
    ];
}
