<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Register\Module;

use Weline\Framework\Console\ConsoleException;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Module\Handle;
use Weline\Framework\Register\Module\Data\DataInterface;

class Install implements DataInterface
{
    /**
     * @DESC         |将模块注册到app/etc/modules.php
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $moduleName
     * @param string|null $version
     * @param string $description
     * @throws ConsoleException
     * @throws \Weline\Framework\App\Exception
     * @throws \ReflectionException
     */
    public static function install(string $moduleName, string $version = '', string $description = '')
    {
        // 注册
        /**@var $moduleHandler Handle*/
        $moduleHandler = ObjectManager::getInstance(Handle::class);
        $moduleHandler->register($moduleName, $version, $description);
    }
}
