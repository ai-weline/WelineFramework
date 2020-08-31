<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/31
 * 时间：20:30
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Manager;


class PluginManager implements ManagerInterface
{
    private static PluginManager $instance;

    private function __clone()
    {
    }

    private function __construct()
    {
    }

    public static function create(string $class, string $method, array $params)
    {
        // TODO: Implement create() method.
    }

    public static function getInstance($class = '')
    {
        if (empty($class)) return isset(self::$instance) ? self::$instance : new self();
    }

    function register(){

    }
}