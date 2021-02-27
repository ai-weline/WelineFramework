<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\System\File;

use Weline\Framework\Register\RegisterInterface;

class Scanner extends Scan
{
    /**
     * @DESC         |扫描vendor供应商目录 模块
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @return array|false
     */
    public function scanAppModules()
    {
        $vendors = $this->scanAppVendors();
        foreach ($vendors as $key => $vendor) {
            unset($vendors[$key]);
            if ($vendor_files = $this->scanVendorModules($vendor)) {
                $vendors[$vendor] = $vendor_files;
            }
        }

        return $vendors;
    }

    /**
     * @DESC         |扫描应用供应商
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @return array|false
     */
    public function scanAppVendors()
    {
        $apps    = $this->scanDir(APP_PATH);
        $vendors = $this->scanDir(BP . 'vendor');

        return array_merge($vendors, $apps);
    }

    /**
     * @DESC         |扫描供应商模块
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param $vendor
     */
    public function scanVendorModules($vendor)
    {
        $app_modules  = $this->scanDir(APP_PATH . $vendor);
        $core_modules = $this->scanDir(BP . 'vendor/' . $vendor);
        $modules      = array_merge($core_modules, $app_modules);
        foreach ($modules as $key => $module) {
            unset($modules[$key]);
            if (file_exists(APP_PATH . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file)) {
                $modules[$module] = $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file;
            }
            // app下的代码优先度更高
            if (! isset($modules[$module])) {
                if (file_exists(BP . 'vendor/' . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file)) {
                    $modules[$module] = $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file;
                }
            }
        }

        return $modules;
    }

    /**
     * @DESC         |扫描带有文件的模块
     *
     * 参数区：
     *
     * @param string $dir
     * @param string $file
     * @return array
     */
    public function scanVendorModulesWithFiles($file = '')
    {
        $vendors         = $this->scanAppVendors();
        $vendors_modules = [];
        foreach ($vendors as $vendor) {
            $app_modules  = $this->scanDir(APP_PATH . $vendor . DIRECTORY_SEPARATOR);
            $core_modules = $this->scanDir(BP . 'vendor' . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR);
            $modules      = array_merge($core_modules, $app_modules);
            foreach ($modules as $key => $module) {
                $app_module_path = APP_PATH . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR;
                unset($modules[$key]);
                if (file_exists($app_module_path . RegisterInterface::register_file)) {
                    if ($file) {
                        if (file_exists($app_module_path . $file)) {
                            $modules[$module] = $app_module_path . $file;
                        }
                    } else {
                        $this->clearDirs();
                        $modules[$module] = $this->scanDirTree($app_module_path);
                        $this->clearDirs();
                    }
                }
                // app下的代码优先度更高
                $vendor_app_module_path = BP . 'vendor/' . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR;
                if (! isset($modules[$module])) {
                    if (file_exists($vendor_app_module_path . RegisterInterface::register_file)) {
                        if ($file) {
                            if (file_exists($vendor_app_module_path . $file)) {
                                $modules[$module] = $vendor_app_module_path . $file;
                            }
                        } else {
                            $this->clearDirs();
                            $modules[$module] = $this->scanDirTree($vendor_app_module_path);
                            $this->clearDirs();
                        }
                    }
                }
            }

            if ($modules) {
                $vendors_modules[$vendor] = $modules;
            }
        }

        return $vendors_modules;
    }
}
