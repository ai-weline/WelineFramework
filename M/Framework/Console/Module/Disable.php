<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/21
 * 时间：12:47
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\Module;


use M\Framework\App\Env;
use M\Framework\Console\CommandAbstract;
use M\Framework\Module\Helper\Data;

class Disable extends CommandAbstract
{

    public function execute($args = array())
    {
        $command = array_shift($args);
        $module_list = Env::getInstance()->getModuleList();
        if(empty($module_list)) {
            $this->printer->error('请先更新模块:bin/m module:upgrade');
            exit();
        }
        if (!empty($args)) {
            foreach ($args as $module) {
                if (isset($module_list[$module])) {
                    $module_list[$module]['status'] = 0;
                    $this->printer->printing('已禁用！', $this->printer->colorize($module, $this->printer::ERROR), $this->printer::ERROR);
                    $this->printer->printList([$module => $module_list[$module]], '=>');
                } else $this->printer->error('不存在的模块:' . $module);
            }
            // 更新模块信息
            $helper = new Data();
            $helper->updateModules($module_list);
            $upgrade = new Upgrade();
            $upgrade->execute();
        } else $this->printer->printList([$command => ['禁用提示：' => $this->printer->colorize('请输入要禁用的模块', $this->printer::ERROR)]]);
    }

    public function getTip(): string
    {
        return '禁用模块';
    }
}