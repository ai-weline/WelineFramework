<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/6
 * 时间：17:52
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Session\Driver;


use M\Framework\Session\SessionInterface;

class Redis extends AbstractSessionDriverHandle implements  DriverInterface
{

    public function __construct(array $config)
    {
    }

    function set($name, $value)
    {
        // TODO: Implement set() method.
    }

    function get($name)
    {
        // TODO: Implement get() method.
    }

    function del($name)
    {
        // TODO: Implement del() method.
    }

    function des()
    {
        // TODO: Implement des() method.
    }

    function open()
    {
        // TODO: Implement open() method.
    }

    function gc(int $sessMaxLifeTime)
    {
        // TODO: Implement gc() method.
    }
}