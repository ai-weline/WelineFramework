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

class PluginTestController extends PluginAbstract
{
    public function beforeDd(Index $object)
    {
        echo 'beforeIndex';

        return $object->index();
    }

    public function aroundDd(Index $object)
    {
    }

    public function afterDd(Index $object, $result)
    {
    }
}
