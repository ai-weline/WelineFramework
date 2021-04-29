<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/4/29
 * 时间：14:05
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Manager;


interface FactoryObjectInterface
{
    /**
     * @DESC         |创建
     *
     * 参数区：
     *
     * @return mixed
     */
    function create();
}