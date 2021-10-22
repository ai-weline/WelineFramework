<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\System;

class Text
{
    static function str_32($str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',$length = 32): string
    {
        $rand = '';
        $max = strlen($str) - 1;
        mt_srand((double)microtime() * 1000000);
        for ($i = 0; $i < $length; $i++) {
            $rand .= $str[mt_rand(0, $max)];
        }
        return $rand;
    }
}