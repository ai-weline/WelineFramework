<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console\Resource\Compiler;

use Weline\Theme\Console\Resource\CompilerInterface;

class Less implements CompilerInterface
{
    protected $less;

    function __init()
    {
        $this->less = new \Less_Parser();
    }

    function compile(string $less_file=null, string $out_file=null)
    {
        if($less_file){
            $this->less->parserFile($less_file, $out_file);
        }
        return true;
    }
}