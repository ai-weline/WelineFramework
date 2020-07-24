<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/11
 * 时间：17:49
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\HelloWorld\Controller\Demo;


use M\Framework\App\Controller\FrontendController;

class Index extends FrontendController
{
    function index()
    {
        p('demo/Index');
        return 'demo/Index';
    }
}