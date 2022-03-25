<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\System\File;

use Weline\Framework\App\Exception;
use Weline\Framework\Register\Register;
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
        $apps    = $this->scanDir(APP_CODE_PATH);
        $vendors = $this->scanDir(VENDOR_PATH);

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
        $app_modules  = $this->scanDir(APP_CODE_PATH . $vendor);
        $core_modules = $this->scanDir(BP . 'vendor/' . $vendor);
        $modules      = array_merge($core_modules, $app_modules);
        foreach ($modules as $key => $module) {
            unset($modules[$key]);
            if (file_exists(APP_CODE_PATH . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file)) {
                $modules[$module] = $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file;
            }
            // app下的代码优先度更高
            if (!isset($modules[$module])) {
                if (file_exists(BP . 'vendor/' . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file)) {
                    $modules[$module] = $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file;
                }
            }
        }

        return $modules;
    }

    /**
     * @DESC          # 扫描带有文件的模块
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/6 22:08
     * 参数区：
     *
     * @param string        $file_or_dir
     * @param \Closure|null $callback
     *
     * @return array
     */
    public function scanVendorModulesWithFiles(string $file_or_dir = '', \Closure $callback = null): array
    {
        $vendors         = $this->scanAppVendors();
        $vendors_modules = [];
        foreach ($vendors as $vendor) {
            $app_modules  = $this->scanDir(APP_CODE_PATH . $vendor . DIRECTORY_SEPARATOR);
            $core_modules = $this->scanDir(VENDOR_PATH . $vendor . DIRECTORY_SEPARATOR);
            $modules      = array_merge($core_modules, $app_modules);
            foreach ($modules as $key => $origin_module_name) {
                $module_name = Register::moduleName($vendor, $origin_module_name);
                // app下的代码优先度更高
                $app_module_path = APP_CODE_PATH . $vendor . DIRECTORY_SEPARATOR . $origin_module_name;
                unset($modules[$key]);
                $app_need_file_or_dir = $app_module_path . DIRECTORY_SEPARATOR . $file_or_dir;
                if (is_dir($app_module_path)) {
                    if (is_file($app_module_path . DIRECTORY_SEPARATOR . RegisterInterface::register_file)) {
                        if (is_file($app_need_file_or_dir)) {
                            $modules[$module_name] = $app_need_file_or_dir;
                        } else {
                            $this->__init();
                            $modules[$module_name] = $this->scanDirTree($app_need_file_or_dir);
                            $this->__init();
                        }
                        continue;
                    }
                }
                // vendor下的代码会被覆盖
                $vendor_app_module_path      = VENDOR_PATH . strtolower($vendor) . DIRECTORY_SEPARATOR . $origin_module_name;
                $vendor_app_need_file_or_dir = $app_module_path . DIRECTORY_SEPARATOR . $file_or_dir;
                if (!isset($modules[$module_name])) {
                    if (is_dir($vendor_app_module_path)) {
                        if (file_exists($vendor_app_module_path . DIRECTORY_SEPARATOR . RegisterInterface::register_file)) {
                            if (is_dir($vendor_app_need_file_or_dir)) {
                                $modules[$module_name] = $vendor_app_need_file_or_dir;
                            } elseif (is_file($vendor_app_need_file_or_dir)) {
                                $modules[$module_name] = $vendor_app_need_file_or_dir;
                            } else {
                                $this->__init();
                                $modules[$module_name] = $this->scanDirTree($vendor_app_need_file_or_dir);
                                $this->__init();
                            }
                            /*try {
                                if(is_file($vendor_app_module_path . $file_or_dir)){
                                    $modules[$module] = $vendor_app_module_path . $file_or_dir;
                                }
                            }catch (Exception){
                                $this->init();
                                $modules[$module] = $this->scanDirTree($vendor_app_module_path . $file_or_dir, 3);
                                $this->init();
                            }*/
                        }
                    }
                }
            }

            if ($modules) {
                $vendors_modules[ucfirst($vendor)] = $modules;
            }
        }

        # 带有回调处理的方法
        if ($callback) {
            $vendors_modules = $callback($vendors_modules);
        }
        return $vendors_modules;
    }
}
