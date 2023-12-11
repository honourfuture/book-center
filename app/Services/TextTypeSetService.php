<?php
/**
 * Created by PhpStorm
 *
 * @author    joy <younghearts2008@gmail.com>
 * Date: 2023/4/28
 * Time: 17:43
 */

namespace App\Services;

class TextTypeSetService
{
    private $treplace = array();  //替换成
    private $delmoreblank = true; //删除连续空格
    private $delchars = array(); //需要删除的字符串
    private $errstartchars = array(); //不能最为段首支付
    private $fmore = array();  //连续字符需要替换
    private $tmore = array();  //连续字符替换成

    public function __construct()
    {
        $this->freplace = array(',', '.', '·', '．', ';', '!', '?', ':', '(', ')');
        $this->treplace = array('，', '。', '。', '。', '；', '！', '？', '：', '（', '）');
        $this->delmoreblank = true;
        $this->delchars = array(' ', '　', "\r");
        $this->errstartchars = array('。', '？', '！', '」', '”', '）');
        $this->fmore = array('.', '。', '-');
        $this->tmore = array('……', '……', '——');
    }

    //排版
    public function doTypeSet($str)
    {
        //$str=str_replace($this->f_replace, $this->t_replace, $str);
        $ret = '';
        $tmpstr = '';
        $tmpstr1 = '';
        $repeatnum = 0; //重复次数TextTypeSetService
        $start = true;  //文章开始标志
        $linestart = true;  //行开始标志
        $sectionstart = true;  //段开始标志
        $strlen = strlen($str);
        for ($i = 0; $i < $strlen; $i++) {
            $tmpstr = $str[$i];
            //判断中英文，取字符
            if (ord($str[$i]) > 0x80 && $i + 1 < $strlen) {
                $tmpstr .= $str[++$i];
            }
            //需要删除的字符
            if (in_array($tmpstr, $this->delchars)) continue;
            //遇到回车设置分段标志
            if ($tmpstr == "\n") {
                $sectionstart = true;
                continue;
            }
            //不允许作为段首的字符
            if ($sectionstart && in_array($tmpstr, $this->errstartchars)) $sectionstart = false;

            //某些重复字符处理
            // $tmpprivate = $repeatnum;
            // if (in_array($tmpstr, $this->fmore)) {
            //     if ($tmpstr == $tmpstr1) {
            //         $repeatnum++;
            //     } else {
            //         $tmpstr1 = $tmpstr;
            //         $repeatnum = 1;
            //     }
            //     continue;
            // }
            // if ($tmpprivate > 0 && $tmpprivate == $repeatnum) {
            //     if ($repeatnum == 1) {
            //         $ret .= $tmpstr1;
            //     } else {
            //         $key = array_search($tmpstr1, $this->fmore);
            //         if ($key) $ret .= $this->tmore[$key];
            //     }
            //     $tmpstr1 = '';
            //     $repeatnum = 0;
            // }
            //段首处理
            if ($sectionstart) {
                if (!$start) $ret .= "\r\n\r\n";
                else $start = false;
                $ret .= '    ';
                $sectionstart = false;
            }
            $ret .= $tmpstr;
        }
        $ret .= $tmpstr1;
        // //最后一个可能缓存的字符
        // if ($repeatnum == 1) {
        //     $ret .= $tmpstr1;
        // } elseif ($repeatnum > 1) {
        //     $key = array_search($tmpstr1, $this->fmore);
        //     print_r($tmpstr1);die;
        //     if ($key) $$ret .= $this->tmore[$key];
        // }

        return $ret;
    }

}
