<?php

declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/5/11
 * 时间：0:02
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Maintenance\Plugin;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;

class Init
{
    public function beforeStart(
        \Weline\Framework\Router\Core $router
    ) {
        if (Env::getInstance()->getConfig('maintenance', false)) {
            throw new Exception(__('程序维护中...'));
        }
    }
}
