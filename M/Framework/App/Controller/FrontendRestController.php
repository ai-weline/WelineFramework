<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/27
 * 时间：16:54
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\App\Controller;


use M\Framework\Controller\AbstractRestController;

class FrontendRestController extends AbstractRestController
{
    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $msg
     * @param bool $data
     * @param int $code
     */
    function error($msg = '错误！', $data = false, int $code = 400)
    {
        die($this->fetch(array('msg' => $msg, 'data' => $data, 'code' => $code)));
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $msg
     * @param bool $data
     * @param int $code
     */
    function errorXml($msg = '错误！', $data = false, int $code = 400)
    {
        die($this->fetch(array('msg' => $msg, 'data' => $data, 'code' => $code)));
    }
}