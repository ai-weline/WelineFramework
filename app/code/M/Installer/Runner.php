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
use M\Installer\RunType\Bin\Commands;
use M\Installer\RunType\Db\InstallConfig;
use M\Installer\RunType\Env\Checker;
use M\Installer\RunType\System\Init;
use M\Installer\RunType\System\Install;

class Runner
{

    function checkEnv(): array
    {
        return (new Checker)->run();
    }

    function installDb(array $params = []): array
    {
        if (!CLI) $params = Request::getInstance('M\\Installer')->getParams();
        return (new InstallConfig())->run($params);
    }

    function systemInstall(): array
    {
        return (new Install())->run();
    }

    function systemCommands(): array
    {
        return (new Commands())->run();
    }

    function systemInit(array $params=[]): array
    {
        if (!CLI) $params = Request::getInstance('M\\Installer')->getParams();
        return (new Init())->run($params);
    }
}