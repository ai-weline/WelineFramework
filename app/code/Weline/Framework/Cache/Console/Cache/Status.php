<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache\Console\Cache;

use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Cache\Scanner;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Cli\Printing;

class Status implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @var Scanner
     */
    private Scanner $scanner;
    private Printing $printing;

    public function __construct(
        Scanner  $scanner,
        Printing $printing
    )
    {
        $this->scanner  = $scanner;
        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        $this->printing->warning(__('模组缓存'));
        $caches = $this->scanner->scanAppCaches();
        /**@var CacheInterface $cache */
        foreach ($caches as $cache_data) {
            $cache = ObjectManager::getInstance(rtrim($cache_data['class'], "Factory") . 'Factory');
            $this->printing->note(
                str_pad($cache->getIdentify(), 45) .
                '=>' . ($cache->getStatus() ? 1 : 0) .'   '.$cache->tip()
            );
        }
        $this->printing->warning(__('框架缓存'));
        $caches = $this->scanner->scanFrameworkCaches();
        /**@var CacheInterface $cache */
        foreach ($caches as $cache_data) {
            $cache = ObjectManager::getInstance(rtrim($cache_data['class'], "Factory") . 'Factory');
            $this->printing->note(
                str_pad($cache->getIdentify(), 45) .
                '=>' . ($cache->getStatus() ? 1 : 0) .'   '.$cache->tip()
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        // TODO: Implement getTip() method.
        return __('缓存状态。[enable/disable]:开启/关闭 [identify]:缓存识别名');
    }
}
