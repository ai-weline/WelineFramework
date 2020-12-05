<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console\Deploy;

use Weline\Framework\Console\CommandAbstract;
use Weline\Framework\FileSystem\App\Scanner as AppScanner;
use Weline\Framework\View\Data\DataInterface;

class Upgrade extends CommandAbstract
{
    public function execute($args = [])
    {
        // 扫描代码
        $scanner = new AppScanner();
        $apps    = $scanner->scanAppModules();
        // 注册模块
        foreach ($apps as $vendor => $modules) {
            foreach ($modules as $name => $register) {
                $this->printer->note($vendor . '_' . $name . '...');
                $module_view_static_dir = $vendor . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . DataInterface::dir . DIRECTORY_SEPARATOR . DataInterface::dir_type_STATICS;
                $module_view_dir        = $vendor . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . DataInterface::dir;
                $origin_view_dir        = APP_PATH . $module_view_static_dir;
                if (is_dir($origin_view_dir)) {
                    $pub_view_dir = PUB . 'static' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $module_view_dir;
                    if (! is_dir($pub_view_dir)) {
                        mkdir($pub_view_dir, 0775, true);
                    }
                    exec(IS_WIN ? "xcopy $origin_view_dir $pub_view_dir" : "cp $origin_view_dir $pub_view_dir -r");
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
