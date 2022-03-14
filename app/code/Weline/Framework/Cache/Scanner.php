<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache;

use Weline\Framework\App\Env;
use Weline\Framework\System\File\Data\File;

class Scanner extends \Weline\Framework\System\File\App\Scanner
{
    const dir = 'Cache';

    public function getCaches()
    {
        $app_caches       = $this->scanAppCaches();
        $framework_caches = $this->scanFrameworkCaches();

        return [
            'app_caches'       => $app_caches,
            'framework_caches' => $framework_caches,
        ];
    }

    /**
     * @DESC         |搜索App所有模块的缓存管理器
     *
     * 参数区：
     */
    public function scanAppCaches()
    {
        $apps       = $this->scanAppModules();
        $app_caches = [];
        foreach ($apps as $vendor => $modules) {
            foreach ($modules as $module_name => $register_file) {
                $relate_scan_path = $vendor . DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . self::dir . DIRECTORY_SEPARATOR;
                $scan_path        = APP_CODE_PATH . $relate_scan_path;
                if (is_file($register_file)&&$cacheManagers = $this->scanDir($scan_path)) {
                    foreach ($cacheManagers as $cacheManager) {
                        $app_caches[] = ['class' => str_replace('/', '\\', $relate_scan_path) . str_replace('.php', '', $cacheManager), 'path' => $scan_path . $cacheManager];
                    }
                }
            }
        }
        return $app_caches;
    }

    /**
     * @DESC         |框架以及第三方Cache
     *
     * 参数区：
     *
     * @return array
     */
    public function scanFrameworkCaches()
    {
        $vendor = $this->scanDirTree(Env::vendor_path);
        // 扫描核心命令
//        $custom = $this->scanDirTree(APP_CODE_PATH);
        $framework = $this->scanDirTree(APP_CODE_PATH . 'Weline' . DIRECTORY_SEPARATOR . 'Framework');
        // 合并
        $dir_files        = array_merge($vendor, $framework);
        $framework_caches = [];
        /** @var $dir_file File[] */
        foreach ($dir_files as $dir => $dir_file) {
            if (is_string($dir) && is_int(strpos($dir, self::dir))) {
                if (IS_WIN) {
                    $dir = str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $dir);
                }
                // 固定格式
                $dir_arr = explode(DIRECTORY_SEPARATOR, $dir);
                if (count($dir_arr) === 4 && self::dir === array_pop($dir_arr)) {
                    $scan_path = APP_CODE_PATH . $dir;
                    if (! is_dir($scan_path)) {
                        $scan_path = Env::vendor_path . $dir;
                    }
                    if ($cacheManagers = $this->scanDir($scan_path)) {
                        foreach ($cacheManagers as $cacheManager) {
                            $framework_caches[] = ['class' => str_replace('/', '\\', $dir) . '\\' . str_replace('.php', '', $cacheManager), 'path' => $scan_path . DIRECTORY_SEPARATOR . $cacheManager];
                        }
                    }
                }
            }
        }

        return $framework_caches;
    }
}
