<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Resource\Compiler;

use Weline\Framework\Resource\CompilerInterface;

class Less implements CompilerInterface
{
    protected $less;

    function __init()
    {
        $this->less = new \lessc();
    }

    function compiler(string $less_file, string $out_file)
    {
        return $this->less->checkedCompile($less_file, $out_file);
    }
}