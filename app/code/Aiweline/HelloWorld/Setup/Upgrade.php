<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/3
 * 时间：15:11
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\HelloWorld\Setup;


use M\Framework\Database\Db\Ddl\Table;
use M\Framework\Setup\Data;
use M\Framework\Setup\UpgradeInterface;

class Upgrade implements UpgradeInterface
{

    function setup(Data\Setup $setup, Data\Context $context)
    {
        $setup->getPrinter()->error('升级操作中...');
    }
}