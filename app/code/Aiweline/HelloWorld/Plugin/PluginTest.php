<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Plugin;

class PluginTest
{
    public function beforeGetName(\Aiweline\HelloWorld\Model\PluginTestModel $object, $a)
    {
        $a .= '我被PluginTest类的beforeGetName修改过';

        return $a;
    }

    public function aroundGetName(\Aiweline\HelloWorld\Model\PluginTestModel $object, \closure $closure, $a)
    {
        $a .= '我被PluginTest类的aroundGetName修改过';

        return $a;
    }

    public function afterGetName(\Aiweline\HelloWorld\Model\PluginTestModel $object, $a, $result)
    {
        $a .= '我被PluginTest类的afterGetName修改过';

        return $a;
    }
}
