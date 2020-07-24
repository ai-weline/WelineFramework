<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/26
 * 时间：16:54
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\HelloWorld\Api\Rest\V1;


use M\Framework\App\Controller\FrontendRestController;

class Index extends FrontendRestController
{

    function getIndex()
    {
        return 'Hello Rest api!--》getIndex';
    }

    function postIndex()
    {
        return 'Hello Rest api!--》postIndex';
    }

    function putIndex()
    {
        return 'Hello Rest api!--》putIndex';
    }

    function deleteIndex()
    {
        return 'Hello Rest api!--》putIndex';
    }

    function updateIndex()
    {
        return 'Hello Rest api!--》updateIndex';
    }
}