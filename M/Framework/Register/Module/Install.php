<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/20
 * 时间：14:03
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Register\Module;


use M\Framework\Console\ConsoleException;
use M\Framework\Module\Handle;
use M\Framework\Register\Module\Data\DataInterface;

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
     * @throws \M\Framework\App\Exception
     * @throws \ReflectionException
     */
    static function install(string $moduleName, string $version='',string $description='')
    {
        // 注册
        $moduleHandler = new Handle();
        $moduleHandler->register($moduleName, $version,$description);
    }

}