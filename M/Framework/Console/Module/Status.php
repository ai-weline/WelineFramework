<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/10
 * 时间：22:28
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\Module;


use M\Framework\Console\CommandAbstract;
use M\Framework\Console\CommandInterface;

class Status extends CommandAbstract
{

    public function execute($args = array())
    {
        array_shift($args);
        $module_list = include APP_ETC_PATH . 'modules.php';
        if (!empty($args)) {
            foreach ($args as $module) {
                if (isset($module_list[$module])) {
                    $this->printer->printList([$module=>$module_list[$module]], '=>');
                } else $this->printer->error(__('不存在的模块:') . $module);
            }
        } else $this->printer->printList($module_list, '=>');
    }

    public function getTip(): string
    {
        return '获取模块列表';
    }
}