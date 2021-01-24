<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache;

use Weline\Framework\App\Env;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\System\File\Data\File;

class Scanner extends \Weline\Framework\System\File\App\Scanner
{
    const dir='Cache';
    public function getCaches()
    {
        $app_caches = $this->scanAppCaches();
        $framework_caches = $this->scanFrameworkCaches();
        p($framework_caches);
    }

    /**
     * @DESC         |搜索App所有模块的缓存管理器
     *
     * 参数区：
     */
    public function scanAppCaches()
    {
        $apps       = $this->scanAppModules();
        p($apps);
        $app_caches = [];
        foreach ($apps as $vendor=>$modules) {
            foreach ($modules as $module_name=>$register_file) {
                $relate_scan_path = $vendor . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . self::dir . DIRECTORY_SEPARATOR;
                $scan_path        = APP_PATH . $relate_scan_path;
                if ($cacheManagers = $this->scanDir($scan_path)) {
                    foreach ($cacheManagers as $cacheManager) {
                        $app_caches[] = ['class'=>$relate_scan_path . $cacheManager, 'path'=>$scan_path . $cacheManager];
                    }
                }
            }
        }

        return $app_caches;
    }

    public function scanFrameworkCaches()
    {
        $vendor = $this->scanDirTree(Env::vendor_path);
        // 扫描核心命令
//        $custom = $this->scanDirTree(APP_PATH);
        $framework = $this->scanDirTree(APP_PATH.'Weline'.DIRECTORY_SEPARATOR.'Framework');
        // 合并
        $dir_files = array_merge($vendor, $framework);
        /** @var $dir_file File[] */
        foreach ($dir_files as $dir => $dir_file) {
            if (is_string($dir) && strstr($dir, self::dir)) {
                if (IS_WIN) {
                    $dir = str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $dir);
                }
                p($dir,1);

            }
        }

        return $commands;
    }
}
