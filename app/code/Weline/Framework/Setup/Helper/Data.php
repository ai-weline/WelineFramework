<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Setup\Helper;

use Weline\Framework\App\Env;
use Weline\Framework\Setup\Data\DataInterface;

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
    public function getSetupClass(string $module_name, string $type = DataInterface::type_INSTALL)
    {
        $module_list = Env::getInstance()->getModuleList();
        $removeFile  = str_replace(DIRECTORY_SEPARATOR, '\\', $module_list[$module_name]['path'] . DIRECTORY_SEPARATOR . DataInterface::dir . DIRECTORY_SEPARATOR . $type);
        if (isset($module_list[$module_name])) {
            if (is_file(APP_PATH . $removeFile)) {
                return APP_PATH . $removeFile;
            }
            if (is_file(BP . 'vendor/' . $removeFile)) {
                return BP . 'vendor/' . $removeFile;
            }
        }

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
    public function getSetupFile(string $module_name, string $type = DataInterface::type_INSTALL)
    {
        $module_list = Env::getInstance()->getModuleList();
        if (isset($module_list[$module_name])) {
            return APP_PATH . $module_list[$module_name]['path'] . DIRECTORY_SEPARATOR . DataInterface::dir . DIRECTORY_SEPARATOR . $type . '.php';
        }

        return false;
    }
}
