<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/4
 * 时间：15:13
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Setup\Helper;


use M\Framework\App\Etc;
use M\Framework\Setup\Data\DataInterface;

class Data
{
    /**
     * @DESC         |获取升级类
     *
     * 参数区：
     *
     * @param string $module_name
     * @param string $type
     * @return bool|string
     */
    function getSetupClass(string $module_name, string $type = DataInterface::type_INSTALL)
    {
        $module_list = Etc::getInstance()->getModuleList();
        if (isset($module_list[$module_name]))
            return str_replace(DIRECTORY_SEPARATOR, '\\', $module_list[$module_name]['path'] . DIRECTORY_SEPARATOR . DataInterface::dir . DIRECTORY_SEPARATOR . $type);
        return false;
    }

    /**
     * @DESC         |获取升级文件
     *
     * 参数区：
     *
     * @param string $module_name
     * @param string $type
     * @return bool|string
     */
    function getSetupFile(string $module_name, string $type = DataInterface::type_INSTALL)
    {
        $module_list = Etc::getInstance()->getModuleList();
        if (isset($module_list[$module_name]))
            return APP_PATH . $module_list[$module_name]['path'] . DIRECTORY_SEPARATOR . DataInterface::dir . DIRECTORY_SEPARATOR . $type . '.php';
        return false;
    }
}