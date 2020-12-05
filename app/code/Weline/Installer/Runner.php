<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Installer;

use Weline\Framework\Http\Request;
use Weline\Installer\RunType\Bin\Commands;
use Weline\Installer\RunType\Db\InstallConfig;
use Weline\Installer\RunType\Env\Checker;
use Weline\Installer\RunType\System\Init;
use Weline\Installer\RunType\System\Install;

class Runner
{
    public function checkEnv(): array
    {
        return (new Checker())->run();
    }

    public function installDb(array $params = []): array
    {
        if (! CLI) {
            $params = Request::getInstance('Weline\\Installer')->getParams();
        }

        return (new InstallConfig())->run($params);
    }

    public function systemInstall(): array
    {
        return (new Install())->run();
    }

    public function systemCommands(): array
    {
        return (new Commands())->run();
    }

    public function systemInit(array $params = []): array
    {
        if (! CLI) {
            $params = Request::getInstance('Weline\\Installer')->getParams();
        }

        return (new Init())->run($params);
    }
}
