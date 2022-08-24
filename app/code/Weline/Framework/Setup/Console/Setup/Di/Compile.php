<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Setup\Console\Setup\Di;

use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\System\File\App\Scanner as AppScanner;

class Compile extends \Weline\Framework\Console\CommandAbstract
{
    /**
     * @inheritDoc
     */
    public function execute(array $args = [])
    {
        // 扫描代码
        $scanner = new AppScanner();
        list($vendor, $registers) = $scanner->scanAppModules();
        $this->printer->note('DI:依赖更新...');
        foreach ($registers as $name => $register) {
            $register = $register['register'];
            if (is_file(APP_CODE_PATH . $register)) {
                $all_modules[$vendor . '_' . $name] = $register;
                require APP_CODE_PATH . $register;
            }
        }
        # 分配编译事件
        /**@var EventsManager $evenManager*/
        $evenManager = ObjectManager::getInstance(EventsManager::class);
        $evenManager->dispatch('Framework_Console::compile');
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return 'DI依赖编译';
    }
}
