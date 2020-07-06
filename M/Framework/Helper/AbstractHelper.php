<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/26
 * 时间：11:41
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Helper;


use M\Framework\App\Etc;
use M\Framework\Output\Debug\Printing;

class AbstractHelper
{
    protected $_debug;
    function __construct()
    {
        $this->_debug = new Printing();
    }
}