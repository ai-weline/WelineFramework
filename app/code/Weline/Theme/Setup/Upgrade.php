<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/3/18
 * 时间：22:30
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Theme\Setup;


use Weline\Framework\Setup\Data;

class Upgrade implements \Weline\Framework\Setup\UpgradeInterface
{

    public function setup(Data\Setup $setup, Data\Context $context): void
    {
        $setup->getDb();
        p();
    }
}