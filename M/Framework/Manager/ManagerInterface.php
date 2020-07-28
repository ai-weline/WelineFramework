<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/6
 * 时间：17:25
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Manager;


interface ManagerInterface
{
    /**
     * @DESC         |创建对象
     *
     * 参数区：
     *
     * @param string $class
     * @param string $method
     * @param array $params
     * @return object
     */
    function create(string $class,string $method,array $params): object;
}