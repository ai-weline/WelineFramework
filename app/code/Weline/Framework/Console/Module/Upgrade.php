<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console\Module;

use Weline\Framework\App\System;
use Weline\Framework\App\Env;
use Weline\Framework\Console\CommandAbstract;
use Weline\Framework\System\File\App\Scanner as AppScanner;
use Weline\Framework\Module\Helper\Data;
use Weline\Framework\Output\Cli\Printing;

class Upgrade extends CommandAbstract
{
    /**
     * @var System
     */
    private System $system;

    /**
     * @var AppScanner
     */
    private AppScanner $scanner;

    /**
     * @var Data
     */
    private Data $data;

    public function __construct(
        Printing $printer,
        AppScanner $scanner,
        Data $data,
        System $system
    ) {
        $this->printer = $printer;
        $this->system  = $system;
        $this->scanner = $scanner;
        $this->data    = $data;
    }

    /**
     * @DESC         |方法描述
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array $args
     * @return mixed|void
     */
    public function execute($args = [])
    {
        // 删除路由文件
        $this->printer->warning('路由更新...', '系统');
        $this->printer->warning('清除文件：');
        foreach (Env::router_files_PATH as $path) {
            $this->printer->warning($path);
            if (is_file($path)) {
                list($out, $var) = $this->system->exec('rm -f ' . $path);
                if ($out) {
                    $this->printer->printList($out);
                }
            }
        }
        $this->printer->warning('generated生成目录code代码清理...', '系统');
        $this->system->exec('rm -rf ' . Env::path_framework_generated_code);
        // 扫描代码
        $apps = $this->scanner->scanAppModules();

        $this->printer->note('模块更新...');
        // 注册模块
        $all_modules = [];
        foreach ($apps as $vendor => $modules) {
            foreach ($modules as $name => $register) {
                $all_modules[$vendor . '_' . $name] = $register;
                if (is_file(APP_PATH . $register)) {
                    require APP_PATH . $register;
                }
                if (is_file(BP . 'vendor/' . $register)) {
                    require BP . 'vendor/' . $register;
                }
            }
        }
        // 更新模块
        $module_list = Env::getInstance()->getModuleList(true);
        if (empty($module_list)) {
            $this->printer->error('请先更新模块:bin/m module:upgrade');
            exit();
        }
        $module_list = array_intersect_key($module_list, $all_modules);

        $this->data->updateModules($module_list);

        $this->printer->note('模块更新完毕！');
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '升级模块';
    }

    /**
     * ----------辅助函数--------------
     */
}
