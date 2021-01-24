<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache\Console\Cache;

use Weline\Framework\Cache\Scanner;

class Clear implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @var Scanner
     */
    private Scanner $scanner;

    public function __construct(
        Scanner $scanner
    ) {
        $this->scanner = $scanner;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        $apps  =$this->scanner->getCaches();
        p($apps);
        // FIXME 先简单清理缓存目录
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '缓存清理。';
    }
}
