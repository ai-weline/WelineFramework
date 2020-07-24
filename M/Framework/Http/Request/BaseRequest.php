<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/3
 * 时间：21:14
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Http\Request;


use M\Framework\Http\Response;

class BaseRequest extends RequestAbstract
{
    private static BaseRequest $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    private function __construct()
    {
    }

    /**
     * @DESC         |获取实例
     *
     * 参数区：
     *
     * @return BaseRequest
     */
    public static function getInstance(): BaseRequest
    {
        if (!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }
}