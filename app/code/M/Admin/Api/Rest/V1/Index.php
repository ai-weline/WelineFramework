<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/8
 * 时间：15:57
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Admin\Api\Rest\V1;


use M\Framework\App\Controller\BackendRestController;

class Index extends BackendRestController
{
    function index()
    {
        $data = ['name' => '后台rest接口！', 'params' => $this->_request->getParams()];
        return $this->fetch($data,self::fetch_XML);
    }
}