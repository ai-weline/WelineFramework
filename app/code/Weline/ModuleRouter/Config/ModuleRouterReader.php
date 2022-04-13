<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\ModuleRouter\Config;

use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\System\File\Scanner;
use Weline\ModuleRouter\Cache\ModuleRouterCache;
use function p;

class ModuleRouterReader extends \Weline\Framework\System\ModuleFileReader
{
    private CacheInterface $moduleRouterCache;

    public function __construct(Scanner $scanner, ModuleRouterCache $moduleRouterCache, string $path = 'Controller' . DIRECTORY_SEPARATOR . 'Router.php')
    {
        parent::__construct($scanner, $path);
        $this->moduleRouterCache = $moduleRouterCache->create();
    }

    public function read(): array
    {
        $cache_key = 'routers_rules_cache';
        if ($router_rules = $this->moduleRouterCache->get($cache_key)) {
            return $router_rules;
        }
        $callback = function ($vendors_modules) {
            foreach ($vendors_modules as $vendor => &$modules) {
                foreach ($modules as $module => &$router_file) {
                    if (empty($router_file)) {
                        unset($modules[$module]);
                    } else {
                        $module_path = str_replace('_', '\\', $module);
                        $namespace = str_replace(VENDOR_PATH, '', $router_file);
                        $namespace = str_replace(APP_CODE_PATH, '', $namespace);
                        $namespace_arr = explode(DIRECTORY_SEPARATOR, $namespace);
                        array_shift($namespace_arr);
                        array_shift($namespace_arr);
                        $namespace = $module_path.'\\'.implode('\\', $namespace_arr);
                        $namespace = str_replace('.php', '', $namespace);
                        $router_file = [
                            'origin'=>$router_file,
                            'class'=>$namespace,
                        ];
                    }
                }
                if (empty($modules)) {
                    unset($vendors_modules[$vendor]);
                }
            }
            return $vendors_modules;
        };
        $data     = $this->getFileList($callback);
        $this->moduleRouterCache->set($cache_key, $data);
        return $data;
    }
}
