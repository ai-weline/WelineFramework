<?php

declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/5/13
 * 时间：16:50
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\DeveloperWorkspace\Controller;

use Weline\Framework\App\Controller\FrontendController;

class Index extends FrontendController
{
    public function index()
    {
        return $this->fetch();
    }
}
