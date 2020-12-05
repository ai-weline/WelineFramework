<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\FileSystem\App;

use Weline\Framework\FileSystem\Scan;
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
            $vendors[$vendor] = $this->scanVendorModules($vendor);
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
}
