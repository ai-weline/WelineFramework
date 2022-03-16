<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\System\File\App;

use Weline\Framework\App\Env;
use Weline\Framework\Register\Register;
use Weline\Framework\System\File\Scan;
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
     * @return array|false
     */
    public function scanAppModules()
    {
        $vendors = $this->scanAllAppVendors();
        foreach ($vendors as $key => $vendor) {
            unset($vendors[$key]);
            // 常规模块
            if ($vendor_module_register_files = $this->scanVendorModules($vendor)) {
                if(isset($vendors[Register::parserModuleName($vendor)])&&$modules = $vendors[Register::parserModuleName($vendor)]){
                    $modules = array_merge($modules,$vendor_module_register_files);
                }else{
                    $modules = $vendor_module_register_files;
                }
                $vendors[Register::parserModuleName($vendor)] = $modules;
            }
            //
        }
        return $vendors;
    }

    /**
     * @DESC         |扫描所有注册文件
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
    public function scanRegisters()
    {
        $registers = $this->scanAllAppVendors();
        foreach ($registers as $key => $vendor) {
            unset($registers[$key]);
            if ($vendor_files = $this->scanVendorModules($vendor)) {
                $registers[$vendor] = $vendor_files;
            }
        }

        return $registers;
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
     * @return array|false
     */
    public function scanAllAppVendors()
    {
        $registers = [];
        foreach (Env::register_FILE_PATHS as $register_type => $register_FILE_PATH) {
            $registers = array_merge($registers, $this->scanDir($register_FILE_PATH));
        }

        return $registers;
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
//        $app_modules = $this->scanDir(APP_CODE_PATH . $vendor);
//        $core_modules = $this->scanDir(Env::path_VENDOR_CODE . $vendor);
//        $theme_modules = $this->scanDir(Env::path_CODE_DESIGN. $vendor);
//        $modules = array_merge($core_modules, $app_modules);
//        $modules = array_merge($modules, $theme_modules);
        $modules = [];
        foreach (Env::register_FILE_PATHS as $register_FILE_PATH) {
            if(is_dir($register_FILE_PATH . $vendor)){
                $modules = array_merge($modules, $this->scanDir($register_FILE_PATH . $vendor));
            }
        }
        foreach ($modules as $key => $module) {
            unset($modules[$key]);
//            // app下的代码
//            if (file_exists(APP_CODE_PATH . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file)) {
//                $modules[$module] = $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file;
//            }
//            // app下的代码优先度更高 这里
//            if (!isset($modules[$module])) {
//                if (file_exists(Env::vendor_path . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file)) {
//                    $modules[$module] = $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file;
//                }
//            }
//            // 扫描 主题
//            if (!isset($modules[$module])) {
//                if (file_exists(BP . 'vendor' . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file)) {
//                    $modules[$module] = $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file;
//                }
//            }
            foreach (Env::register_FILE_PATHS as $type=>$register_FILE_PATH) {
                $app_module_path = $register_FILE_PATH . $vendor . DIRECTORY_SEPARATOR . $module ;
                if(is_dir($app_module_path)){
                    $register = $app_module_path. DIRECTORY_SEPARATOR . RegisterInterface::register_file;
                    if (is_file($register)) {
                        $modules[Register::parserModuleName($module)] = $register;
                    }
                }

            }
        }
        return $modules;
    }
}
