<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console\Deploy;

use Weline\Framework\App\Env;
use Weline\Framework\App\System;
use Weline\Framework\Console\CommandAbstract;
use Weline\Framework\System\File\App\Scanner as AppScanner;
use Weline\Framework\View\Data\DataInterface;

class Upgrade extends CommandAbstract
{
    /**
     * @var AppScanner
     */
    private AppScanner $scanner;

    /**
     * @var System
     */
    private System $system;

    public function __construct(
        AppScanner $scanner,
        System $system
    ) {
        $this->scanner = $scanner;
        $this->system  = $system;
    }

    public function execute($args = [])
    {
        // 扫描代码
        $apps = $this->scanner->scanAppModules();
        // 注册模块
        foreach ($apps as $vendor => $modules) {
            foreach ($modules as $name => $register) {
                $this->printer->note($vendor . '_' . $name . '...');
                $module_view_static_dir = $vendor . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . DataInterface::dir . DIRECTORY_SEPARATOR . DataInterface::dir_type_STATICS;
                $module_view_dir        = $vendor . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . DataInterface::dir . DIRECTORY_SEPARATOR;
                // windows的文件复制兼容
                if (IS_WIN) {
                    $module_view_dir .= DataInterface::dir_type_STATICS . DIRECTORY_SEPARATOR;
                }
                $origin_view_dir = APP_PATH . $module_view_static_dir;
                if (is_dir($origin_view_dir)) {
                    // 主题配置
                    $theme_dir    = Env::getInstance()->getConfig('theme', 'default');
                    $pub_view_dir = PUB . 'static' . DIRECTORY_SEPARATOR . $theme_dir . DIRECTORY_SEPARATOR . $module_view_dir;
                    if (! is_dir($pub_view_dir)) {
                        mkdir($pub_view_dir, 0775, true);
                    }
                    list($out, $vars) = $this->system->exec("cp $origin_view_dir $pub_view_dir -r");
                }
            }
        }
        $this->printer->success('静态文件部署完毕！');
    }

    public function getTip(): string
    {
        return '静态资源同步更新。';
    }
}
