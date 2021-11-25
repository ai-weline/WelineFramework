<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console\Resource\Compiler;

use Weline\Theme\Console\Resource\Compiler\RequireJs\Compiler;

class RequireJs extends Compiler
{
    public function compile(string $source_file = null, string $out_file = null)
    {
        # require.config.js处理
        $this->reader->setFile('require.config.js');
        parent::compile();
    }
}