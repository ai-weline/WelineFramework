<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/28
 * 时间：21:10
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\Deploy\Mode;


use M\Framework\App\Env;
use M\Framework\Console\CommandAbstract;

class Show extends CommandAbstract
{

    public function execute($args = array())
    {
        $this->printer->success('当前部署模式：' . Env::getInstance()->getConfig('deploy'));
    }

    public function getTip(): string
    {
        return '查看部署环境';
    }
}