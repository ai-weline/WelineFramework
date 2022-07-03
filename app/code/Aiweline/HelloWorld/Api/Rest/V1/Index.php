<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Api\Rest\V1;

use Weline\Framework\App\Controller\FrontendRestController;
use Weline\Framework\DataObject\DataObject;

class Index extends FrontendRestController
{
    public function getIndex()
    {
        return __('Hello Rest api!--》getIndex');
    }

    public function postIndex()
    {
        return 'Hello Rest api!--》postIndex';
    }

    public function putIndex()
    {
        return 'Hello Rest api!--》putIndex';
    }

    public function deleteIndex()
    {
        return 'Hello Rest api!--》putIndex';
    }

    public function updateIndex()
    {
        return 'Hello Rest api!--》updateIndex';
    }
}
