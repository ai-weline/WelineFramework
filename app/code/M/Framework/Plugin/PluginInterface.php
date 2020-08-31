<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/31
 * 时间：22:04
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Plugin;


interface PluginInterface
{
    const dir = 'Plugin';

    /**
     * @DESC         |设置插件顺序
     *
     * 参数区：
     *
     * @return int
     */
    function orderNumber(): int;

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return bool
     */
    function isEnable(): bool;
}