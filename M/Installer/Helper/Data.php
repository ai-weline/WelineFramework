<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/7
 * 时间：21:56
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Installer\Helper;

use \M\Installer\Helper\InstallData;

class Data
{
    protected InstallData $installData;

    function __construct(
        InstallData $installData
    )
    {
        $this->installData = $installData;
    }

    function getCheckEnv()
    {
        return $this->installData->getData('env');
    }

    function getDbTables()
    {
        $db = $this->installData->getData('db');
        return $db['tables'];
    }
}