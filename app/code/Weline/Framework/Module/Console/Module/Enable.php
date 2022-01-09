<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Module\Console\Module;

use Weline\Framework\App\Env;
use Weline\Framework\Console\Command;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Module\Helper\Data;

class Enable extends Command
{
    public function execute($args = [])
    {
        $command     = array_shift($args);
        $module_list = Env::getInstance()->getModuleList();
        if (empty($module_list)) {
            $this->printer->error('请先更新模块:bin/m module:upgrade');
            exit();
        }
        if (! empty($args)) {
            foreach ($args as $module) {
                if (isset($module_list[$module])) {
                    $module_list[$module]['status'] = 1;
                    $this->printer->printing('已启用！', $this->printer->colorize($module, $this->printer::SUCCESS), $this->printer::ERROR);
                    $this->printer->printList([$module => $module_list[$module]], '=>');
                } else {
                    $this->printer->error('不存在的模块:' . $module);
                }
            }
            // 更新模块信息
            $helper = new Data();
            $helper->updateModules($module_list);
            /**@var $upgrade Upgrade*/
            $upgrade = ObjectManager::getInstance(Upgrade::class);
            $upgrade->execute();
        } else {
            $this->printer->printList([$command => ['启用提示：' => $this->printer->colorize('请输入要启用的模块', $this->printer::ERROR)]]);
        }
    }

    public function getTip(): string
    {
        return '模块启用';
    }
}
