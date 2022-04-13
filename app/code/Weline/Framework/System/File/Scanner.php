<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\System\File;

use Weline\Framework\Register\Register;
use Weline\Framework\Register\RegisterInterface;
use Weline\Framework\System\File\Data\File;

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

    /**
     * @DESC          # 扫描规则目录的代码文件
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/4/12 22:55
     * 参数区：
     *
     * @param string        $file_or_dir 示例：*\Framework\*\etc\event.xml
     * @param \Closure|null $callback
     *
     * @return array
     */
    public function scanCodeFiles(string $file_or_dir = '', \Closure $callback = null): array
    {
        $file_or_dir             = trim($file_or_dir, DIRECTORY_SEPARATOR);
        $file_or_dir_arr         = explode(DIRECTORY_SEPARATOR, $file_or_dir);
        $file_or_dir_path_length = count($file_or_dir_arr);# 目录深度
        $file_or_dir_last        = array_pop($file_or_dir_arr);


        $this->__init();
        $app_modules = $this->scanDirTree(APP_CODE_PATH);
        $this->__init();
        $core_modules = $this->scanDirTree(VENDOR_PATH);
        $modules      = array_merge($core_modules, $app_modules);
        $vendors_modules = [];
//        p($modules);
        /**@var File $file */
        foreach ($modules as $dir => $files) {
            foreach ($files as $file) {
                $file_arr = explode(DIRECTORY_SEPARATOR, $file->getRelate());
                if ($file_or_dir_path_length === count($file_arr) && $file_or_dir_last === $file->getBasename()) {
                    $vendor                                                               = array_shift($file_arr);
                    $vendors_modules[$vendor][implode('_', array_slice($file_arr, 0, 2))] = $file->getOrigin();
                }
            }
        }

        # 带有回调处理的方法
        if ($callback) {
            $vendors_modules = $callback($vendors_modules);
        }
        return $vendors_modules;
    }

    /**
     * @DESC          # 根据匹配规则目录扫描代码文件
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/4/12 22:52
     * 参数区：
     *
     * @param array         $pattern_dirs ['*\Framework\*\etc\event.xml','*\Framework\*\etc\module.xml']
     * @param \Closure|null $callback
     *
     * @return array
     */
    public function scanFilesWithPatternDirs(array $pattern_dirs = [], \Closure $callback = null): array
    {
        $vendors_modules = [];
        foreach ($pattern_dirs as $pattern_dir) {
            $file_or_dir             = trim($pattern_dir, DIRECTORY_SEPARATOR);
            $file_or_dir_arr         = explode(DIRECTORY_SEPARATOR, $file_or_dir);
            $file_or_dir_path_length = count($file_or_dir_arr);# 目录深度
            $file_or_dir_last        = array_pop($file_or_dir_arr);

            $this->__init();
            $app_modules = $this->scanDirTree(APP_CODE_PATH);
            $this->__init();
            $core_modules = $this->scanDirTree(VENDOR_PATH);
            $modules      = array_merge($core_modules, $app_modules);
            /**@var File $file */
            foreach ($modules as $dir => $files) {
                foreach ($files as $file) {
                    $file_arr = explode(DIRECTORY_SEPARATOR, $file->getRelate());
                    if ($file_or_dir_path_length === count($file_arr) && $file_or_dir_last === $file->getBasename()) {
                        $vendor                                                               = array_shift($file_arr);
                        $vendors_modules[$vendor][implode('_', array_slice($file_arr, 0, 2))] = $file->getOrigin();
                    }
                }
            }
        }

        # 带有回调处理的方法
        if ($callback) {
            $vendors_modules = $callback($vendors_modules);
        }
        return $vendors_modules;
    }
}
