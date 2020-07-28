<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/3
 * 时间：15:46
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\App\Env;


use M\Framework\FileSystem\Io\File;

class Modules
{
    /**
     * @DESC         |获取已经安装的模块列表
     *
     * 参数区：
     *
     * @return array
     */
    function getList()
    {
        $file = new File();
        $file->processFile(APP_ETC_PATH . 'modules.php');
        $modules_data = include APP_ETC_PATH . 'modules.php';
        return is_array($modules_data) ? $modules_data : array();
    }
}