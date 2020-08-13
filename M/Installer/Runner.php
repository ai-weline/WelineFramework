<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/7
 * 时间：22:11
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Installer;


use M\Framework\Http\Request;
use M\Framework\Output\Cli\Printing;
use M\Installer\RunType\Db\Installer;
use M\Installer\RunType\Env\Checker;

class Runner
{

    function checkEnv(Checker $envChecker): array
    {
        return $envChecker->run();
    }

    function installDb(Installer $installer): array
    {
        return $installer->run();
    }
}