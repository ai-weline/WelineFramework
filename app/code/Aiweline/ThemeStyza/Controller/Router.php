<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/9/11 13:06:06
 */

namespace Aiweline\ThemeStyza\Controller;

class Router implements \Weline\Framework\Router\RouterInterface
{

    /**
     * @inheritDoc
     */
    public static function process(string &$path, array &$rule)
    {
        $flag = '/styza';
        if (str_contains($path, $flag)||str_contains($path, strtolower($flag))) {
            $path_arr      = explode($flag, $path);
            $function_path = array_pop($path_arr);
            if ($function_path = trim($function_path, '/')) {
                $path           = $flag;
                $rule['template'] = $function_path;
            }
        }
    }
}