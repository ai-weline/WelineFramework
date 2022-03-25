<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Frontend\Plugin;

use Weline\Backend\Config\KeysInterface;
use Weline\Backend\Model\Config;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Frontend\Cache\CacheFactory;

class StarPage
{
    private CacheInterface $cache;

    public function __construct(
        CacheFactory $cacheFactory
    ) {
        $this->cache = $cacheFactory->create();
    }

    public function afterProcessUrl(\Weline\Framework\Router\Core $core, $result)
    {
        if (empty($result)) {
            if ($result = $this->cache->get(KeysInterface::cache_start_page_path)) {
            } else {
                /**@var Config $configModel */
                $configModel = ObjectManager::getInstance(Config::class);
                $result      = $configModel->getConfig(KeysInterface::key_start_page_path, KeysInterface::start_module);
                $this->cache->set(KeysInterface::cache_start_page_path, $result);
            }
        }
        return $result??'';
    }
}
