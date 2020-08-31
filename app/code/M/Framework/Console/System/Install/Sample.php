<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/29
 * 时间：22:08
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\System\Install;


use M\Framework\Console\CommandAbstract;

class Sample extends CommandAbstract
{

    public function execute($args = array())
    {
        $this->printer->note('安装命令示例：');
        $this->printer->success('bin/m system:install \
--db-type=mysql \
--db-hostname=127.0.0.1 \
--db-database=m_dev \
--db-username=m_dev \
--db-password=ShP5T7yzNMs87ZDp
            ');
        exit();
    }

    public function getTip(): string
    {
        return '安装脚本样例';
    }
}