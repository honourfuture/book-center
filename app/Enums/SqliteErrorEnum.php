<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 23:37
 */

namespace App\Enums;

class SqliteErrorEnum extends BaseEnum
{
    const ERROR_CODE = [
        0 => '未知错误',
        101 => '子窗口冲突',
        102 => '检查子窗口冲突失败',
        120 => '对比最新章节失败',
        121 => '空章节',
        122 => '检查到重复章节',
        124 => '只采集文字章节时发现图片',
        125 => '设置不添加新书',
        130 => '限制章节字数小于多少字的章节',
        131 => '131 章节数量小于限制',
        132 => '对比最新章节成功！但需要采集到章节数超限。',
        134 => '限制小说_黑名单',
        136 => '过滤分卷名',
        137 => '章节名过滤（章节名过滤作者名、自定义过滤）',
        200 => '小说信息页发生问题',
        210 => '小说目录页发生问题',
        214 => '章节组为空',
        220 => '小说内容页发生问题',
        410 => '操作本站小说列表发生问题',
        420 => '操作本站小说信息发生问题',
        430 => '操作本站章节列表发生问题',
        440 => '操作本站章节信息发生问题',
        441 => 'InsertChapter发生问题',
        442 => 'UpdateChapter发生问题'
    ];
}