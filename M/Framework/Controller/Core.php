<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/27
 * 时间：12:21
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Controller;


use M\Framework\App\Exception;
use M\Framework\Http\Request;
use M\Framework\View\Data\DataInterface;
use ReflectionObject;

class Core implements Data\DataInterface
{
    protected Request $_request;
    function __construct()
    {
        $ctl_class = explode('\\', get_class($this));
        $module_path = array_shift($ctl_class) . '\\' . array_shift($ctl_class);
        $this->_request = Request::getInstance($module_path);
    }
}