<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Plugin;

class PluginTestModel
{
    public function beforeGetName($object, $a)
    {
        $a[] = '我是beforeGetName修改过的插件';

        return $a;
    }

    public function aroundGetName($object, \closure $closure, $a)
    {
        $a[] = '我是aroundGetName修改过的插件';

        return $a;
    }

    public function afterGetName($object, $a)
    {
        $a[] = '我是afterGetName修改过的插件';

        return $a;
    }
}
