<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/9/10 15:53:32
 */

namespace Aiweline\Index\Controller;

class Index extends \Weline\Framework\App\Controller\FrontendController
{
    function get()
    {
        return $this->fetch();
    }
}