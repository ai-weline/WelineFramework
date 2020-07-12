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


use M\Framework\Console\CommandAbstract;

class Disable extends CommandAbstract
{

    public function execute($args=array())
    {
        p($args);
    }

    public function getTip(): string
    {
        return '禁用模块';
    }
}