<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/20
 * 时间：12:26
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\FileSystem\App;


use M\Framework\App;
use M\Framework\FileSystem\Scan;
use M\Framework\Register\RegisterInterface;

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
    function scanAppModules()
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
        $apps = $this->scanDir(APP_PATH);
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
        $app_modules = $this->scanDir(APP_PATH . $vendor);
        $core_modules = $this->scanDir(BP . 'vendor/'.$vendor);
        $modules = array_merge($core_modules,$app_modules);
        foreach ($modules as $key => $module) {
            unset($modules[$key]);
            if (file_exists(APP_PATH . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file))
                $modules[$module] = $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file;
            // app下的代码优先度更高
            if (!isset($modules[$module]))
                if (file_exists(BP . 'vendor/' . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file))
                    $modules[$module] = $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . RegisterInterface::register_file;
        }
        return $modules;
    }
}