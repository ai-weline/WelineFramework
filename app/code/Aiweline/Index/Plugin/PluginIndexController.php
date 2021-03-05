<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Index\Plugin;

use Aiweline\Index\Controller\Index;
use Weline\Framework\Plugin\PluginAbstract;

class PluginIndexController extends PluginAbstract
{
    public function beforeIndex(Index $object,$e,$test,$arr)
    {
        echo $e;die;
        return get_class($object);
    }

//    public function aroundIndex(Index $object)
//    {
//    }
//
//    public function afterIndex(Index $object, $result)
//    {
//    }
}
