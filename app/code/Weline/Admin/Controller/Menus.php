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
use Weline\Framework\App\Exception;
use Weline\Framework\Manager\ObjectManager;

class Menus extends BaseController
{

    function index()
    {
        /**@var Menu $menu */
        $menu = ObjectManager::getInstance(Menu::class);
        $menu->pagination(
            intval($this->_request->getParam('page', 1)),
            intval($this->_request->getParam('pageSize', 2)),
            $this->_request->getParams()
        )->select();
        $this->assign('menus', $menu->fetch());
        $this->assign('pagination', $menu->getPagination());
        return $this->fetch();
    }

    function postDelete()
    {
        try {
            if ($id = $this->_request->getPost('id', 0)) {
                /**@var Menu $menu */
                $menu = ObjectManager::getInstance(Menu::class)->load($id);
                if ($menu->isSystem()) throw new Exception(__('系统菜单无法删除！'));
                $menu->delete();
                return $this->fetchJson(['code' => 200, 'msg' => __('删除成功！'), 'data' => []]);
            } else {
                return $this->fetchJson(['code' => 403, 'msg' => __('关键参数ID不存在！'), 'data' => []]);
            }
        } catch (\Exception $exception) {
            return $this->fetchJson(['code' => 403, 'msg' => $exception->getMessage(), 'data' => []]);
        }
    }
}