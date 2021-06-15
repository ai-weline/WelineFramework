<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console\Setup\Di;

use Weline\Framework\System\File\App\Scanner as AppScanner;

class Compile extends \Weline\Framework\Console\CommandAbstract
{
    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        // 扫描代码
        $scanner = new AppScanner();
        $apps = $scanner->scanAppModules();

        $this->printer->note('DI:插件更新...');
        // TODO 扫描插件
        $all_plugins = [];
        foreach ($apps as $vendor => $modules) {
            foreach ($modules as $name => $register) {
                if (@is_file(APP_PATH . $register)) {
                    $all_modules[$vendor . '_' . $name] = $register;
                    require APP_PATH . $register;
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return 'DI依赖编译';
    }
}
