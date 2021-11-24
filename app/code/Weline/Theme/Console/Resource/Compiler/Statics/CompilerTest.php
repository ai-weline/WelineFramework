<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console\Resource\Compiler\Statics;

use PHPUnit\Framework\TestCase;
use Weline\Framework\Manager\ObjectManager;
use Weline\Theme\Console\Resource\Compiler\Statics\Compiler;

class CompilerTest extends TestCase
{

    public function testCompiler()
    {
        /**@var Compiler $complier*/
        $complier = ObjectManager::getInstance(Compiler::class);
        $complier->compile();
    }
}
