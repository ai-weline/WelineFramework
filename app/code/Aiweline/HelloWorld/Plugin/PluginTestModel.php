<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/3/6
 * 时间：21:12
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\HelloWorld\Plugin;


class PluginTestModel
{
    function beforeGetName($object, $a)
    {
        $a[] = '我是beforeGetName修改过的插件';
        return $a;
    }

    function aroundGetName($object, \closure $closure,$a)
    {
        $a[] = '我是aroundGetName修改过的插件';
        return $a;
    }

    function afterGetName($object, $a)
    {
        $a[] = '我是afterGetName修改过的插件';
        return $a;
    }
}