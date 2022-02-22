<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller;

use Weline\Backend\Model\Menu;
use Weline\Framework\Manager\ObjectManager;

class Menus extends BaseController
{

    function index()
    {
        /**@var Menu $menu */
        $menu  = ObjectManager::getInstance(Menu::class);
        $menus = $menu->page($this->_request->getGet('page', 1), $this->_request->getGet('pageSize', 10))
                      ->select()
                      ->fetch();
        $this->assign('menus', $menus);
        return $this->fetch();
    }
}