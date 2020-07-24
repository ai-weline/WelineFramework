<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/23
 * 时间：23:19
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\NewsSource\Controller;


use M\Framework\App\Controller\FrontendController;

class Index extends FrontendController
{
    function index()
    {
        return '新闻中心首页！';
    }
}