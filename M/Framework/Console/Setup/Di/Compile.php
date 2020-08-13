<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/31
 * 时间：22:13
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\Setup\Di;


use M\Framework\FileSystem\App\Scanner as AppScanner;

class Compile extends \M\Framework\Console\CommandAbstract
{

    /**
     * @inheritDoc
     */
    public function execute($args = array())
    {
        // 扫描代码
        $scanner = new AppScanner();
        $apps = $scanner->scanAppModules();

        $this->printer->note('DI:插件更新...');
        // 注册模块
        $all_plugins = [];
        foreach ($apps as $vendor => $modules) {
            foreach ($modules as $name => $register) {
                $all_modules[$vendor . '_' . $name] = $register;
                require APP_PATH . $register;
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