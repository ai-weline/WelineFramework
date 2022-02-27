<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller\System;

use Weline\Admin\Controller\BaseController;
use Weline\Framework\App\Env;

class Modules extends BaseController
{
    function getIndex()
    {
        $this->assign('modules', Env::getInstance()->getModuleList());
        return $this->fetch();
    }
}