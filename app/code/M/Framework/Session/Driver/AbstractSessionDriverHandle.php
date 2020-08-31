<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/7
 * 时间：18:59
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Session\Driver;


use M\Framework\Session\SessionInterface;

abstract class AbstractSessionDriverHandle implements SessionInterface, DriverInterface
{
    protected array $config;

    function __construct(array $config)
    {
        $this->config = $config;
        session_set_save_handler(
            array(&$this, "open"),
            array(&$this, "del"),
            array(&$this, "set"),
            array(&$this, "get"),
            array(&$this, "des"),
            array(&$this, "gc")
        );
    }
}