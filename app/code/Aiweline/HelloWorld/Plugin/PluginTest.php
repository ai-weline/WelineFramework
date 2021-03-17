<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Plugin;

class PluginTest
{
    public function beforeGetName($object, $a)
    {
        $a[] = '我被PluginTest类的beforeGetName修改过';

        return $a;
    }

    public function aroundGetName($object, \closure $closure, $a)
    {
        $a[] = '我被PluginTest类的aroundGetName修改过';

        return $a;
    }

//    public function afterGetName($object, $a)
//    {
//        $a[] = '我被PluginTest类的afterGetName修改过';
//
//        return $a;
//    }
}
