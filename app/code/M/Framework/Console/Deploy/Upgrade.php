<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/28
 * 时间：22:34
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\Deploy;


use M\Framework\Console\CommandAbstract;
use M\Framework\FileSystem\App\Scanner as AppScanner;
use M\Framework\View\Data\DataInterface;

class Upgrade extends CommandAbstract
{

    public function execute($args = array())
    {
        // 扫描代码
        $scanner = new AppScanner();
        $apps = $scanner->scanAppModules();
        // 注册模块
        foreach ($apps as $vendor => $modules) {
            foreach ($modules as $name => $register) {
                $this->printer->note($vendor . '_' . $name . '...');
                $module_view_static_dir = $vendor . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . DataInterface::dir . DIRECTORY_SEPARATOR . DataInterface::dir_type_STATICS;
                $module_view_dir = $vendor . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . DataInterface::dir;
                $origin_view_dir = APP_PATH . $module_view_static_dir;
                if (is_dir($origin_view_dir)) {
                    $pub_view_dir = PUB . 'static' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . $module_view_dir;
                    if (!is_dir($pub_view_dir)) mkdir($pub_view_dir, 0775, true);
                    exec("cp $origin_view_dir $pub_view_dir -r");
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