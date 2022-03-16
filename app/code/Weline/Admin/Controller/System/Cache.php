<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller\System;

use Weline\Framework\Cache\Scanner;

class Cache extends \Weline\Admin\Controller\BaseController
{
    private Scanner $scanner;

    function __construct(Scanner $scanner) { $this->scanner = $scanner; }

    function index()
    {
        $cache_key = 'system_caches_key';
        $data      = $this->cache->get($cache_key);
        // TODO 等待解决缓存引发部署文件更新问题
        if (empty($data)) {
            $frameworkCaches = $this->scanner->scanFrameworkCaches();
            $appCaches       = $this->scanner->scanAppCaches();
            $data            = ['framework' => $frameworkCaches, 'app' => $appCaches];
            $this->cache->set($cache_key,$data);
        }
        $this->assign('caches', $data);
        return $this->fetch();
    }
}