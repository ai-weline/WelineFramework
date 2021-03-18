<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Installer;

use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Installer\RunType\Bin\Commands;
use Weline\Installer\RunType\Db\InstallConfig;
use Weline\Installer\RunType\Env\Checker;
use Weline\Installer\RunType\System\Init;
use Weline\Installer\RunType\System\Install;

class Runner
{
    public function checkEnv(): array
    {
        /**@var $checker Checker */
        $checker = ObjectManager::getInstance(Checker::class);

        return $checker->run();
    }

    public function installDb(array $params = []): array
    {
        if (! CLI) {
            $params = Request::getInstance('Weline\\Installer')->getParams();
        }
        /**@var $installConfig InstallConfig */
        $installConfig = ObjectManager::getInstance(InstallConfig::class);

        return $installConfig->run($params);
    }

    public function systemInstall(): array
    {
        /**@var $install Install */
        $install = ObjectManager::getInstance(Install::class);

        return $install->run();
    }

    public function systemCommands(): array
    {
        /**@var $commands Commands */
        $commands = ObjectManager::getInstance(Commands::class);

        return $commands->run();
    }

    public function systemInit(array $params = []): array
    {
        if (! CLI) {
            $params = Request::getInstance('Weline\\Installer')->getParams();
        }
        /**@var $init Init */
        $init = ObjectManager::getInstance(Init::class);

        return $init->run($params);
    }
}
