<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache\Console\Cache;

use Weline\Framework\Cache\Scanner;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Cli\Printing;

class Flush implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @var Scanner
     */
    private Scanner $scanner;

    /**
     * @var Printing
     */
    private Printing $printing;

    /**
     * Flush 初始函数...
     * @param Scanner $scanner
     * @param Printing $printing
     */
    public function __construct(
        Scanner $scanner,
        Printing $printing
    ) {
        $this->scanner  = $scanner;
        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        // FIXME 准备缓存拓展,允许支持第三方自定义缓存插件
//        /**@var EventsManager $eventsManager */
//        $eventsManager = ObjectManager::getInstance(EventsManager::class);
//        $eventsManager->dispatch('WelineFrame');
        $system_cache_dir = BP . 'var' . DIRECTORY_SEPARATOR . 'cache';
        $var_dirs = $this->scanner->scanDir($system_cache_dir);
        foreach ($var_dirs as $var_dir) {
            $cache_dir = $system_cache_dir.DIRECTORY_SEPARATOR.$var_dir;
            p($cache_dir);
        }
        $this->printing->success(__('缓存已清理！'));
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '缓存刷新。';
    }
}
