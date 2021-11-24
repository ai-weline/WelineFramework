<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console\Resource\Compiler;

class RequireJs extends Statics
{
    public function compile(string $source_file = null, string $out_file = null)
    {
        # require.config.js处理
        $this->reader->setFile('require.config.js');
        $data = parent::compile();
        p($data);

    }
}