<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/7
 * 时间：23:15
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\Admin\Controller;



use M\Framework\App\Controller\BackendController;

class Index extends BackendController
{
    function index()
    {
        p('admin首页！');
    }
}