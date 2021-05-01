<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Plugin;

class PluginTestModel
{
    public function beforeGetName(\Aiweline\HelloWorld\Model\PluginTestModel $object, $a)
    {
        $a[] = '我被PluginTestModel类abeforeGetName修改过';

        return $a;
    }

    public function aroundGetName($object, \closure $closure, $a)
    {
        $a[] = '我被PluginTestModel类aroundGetName修改过';

        return $a;
    }

    public function afterGetName($object, $a)
    {
        $a[] = '我被PluginTestModel类afterGetName修改过';

        return $a;
    }
}
