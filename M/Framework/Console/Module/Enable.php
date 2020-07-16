<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/13
 * 时间：2:03
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\Module;


use M\Framework\Console\Command;
use M\Framework\Module\Helper\Data;

class Enable extends Command
{

    public function execute($args = array())
    {
        $command = array_shift($args);
        $module_list = include APP_ETC_PATH . 'modules.php';
        if (!empty($args)) {
            foreach ($args as $module) {
                if (isset($module_list[$module])) {
                    $module_list[$module]['status'] = 1;
                    $this->printer->printing('已启用！', $this->printer->colorize($module, $this->printer::SUCCESS), $this->printer::ERROR);
                    $this->printer->printList([$module => $module_list[$module]], '=>');
                } else $this->printer->error('不存在的模块:' . $module);
            }
            // 更新模块信息
            $helper = new Data();
            $helper->updateModules($module_list);
            $upgrade = new Upgrade();
            $upgrade->execute();
        } else $this->printer->printList([$command => ['启用提示：' => $this->printer->colorize('请输入要启用的模块', $this->printer::ERROR)]]);
    }

    public function getTip(): string
    {
        return '模块启用';
    }
}