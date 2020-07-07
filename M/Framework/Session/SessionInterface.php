<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/6
 * 时间：15:11
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Session;


interface SessionInterface
{
    function open();

    function set($name, $value);

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param $name
     * @return mixed
     */
    function get($name);

    function del($name);

    function des();

    function gc(int $sessMaxLifeTime);
}