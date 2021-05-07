<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache\Console\Cache;

use Weline\Framework\Cache\Scanner;

class Status implements \Weline\Framework\Console\CommandInterface
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
        $caches = $this->scanner->scanAppCaches();
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        // TODO: Implement getTip() method.
        return __('查看缓存状态！');
    }
}
