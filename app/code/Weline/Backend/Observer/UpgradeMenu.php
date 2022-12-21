<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\Observer;

use Weline\Backend\Config\MenuXmlReader;
use Weline\Backend\Model\Menu;
use Weline\Framework\Event\Event;

class UpgradeMenu implements \Weline\Framework\Event\ObserverInterface
{
    private \Weline\Backend\Model\Menu $menu;
    private MenuXmlReader $menuReader;

    public function __construct(
        \Weline\Backend\Model\Menu $menu,
        MenuXmlReader              $menuReader
    )
    {
        $this->menu       = $menu;
        $this->menuReader = $menuReader;
    }

    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        $modules_xml_menus = $this->menuReader->read();
        $this->menu->query('TRUNCATE TABLE ' . $this->menu->getTable());
        # 先更新顶层菜单
        foreach ($modules_xml_menus as $module => &$menus) {
            foreach ($menus['data'] as $key => $menu) {
                if (empty($menu['parent'])) {
                    # 清空查询条件
                    $this->menu->clearData();
                    $menu[Menu::fields_MODULE]        = $module;
                    $menu[Menu::fields_PARENT_SOURCE] = $menu['parent'] ?? '';
                    unset($menu['parent']);
                    # 1 存在父资源 检查父资源的 ID
                    if (isset($menu[Menu::fields_PARENT_SOURCE]) && $parent = $menu[Menu::fields_PARENT_SOURCE]) {
                        $parent = $this->menu->where(Menu::fields_SOURCE, $parent)->find()->fetch();
                        if ($pid = $parent->getId()) {
                            $menu[Menu::fields_PID] = $pid;
                        } else {
                            $menu[Menu::fields_PID] = 0;
                        }
                    }
                    $menu[Menu::fields_PID] = $menu[Menu::fields_PID] ?? 0;
                    # 先查询一遍
                    /**@var Menu $menuModel */
                    $this->menu->clearData();
                    $menuModel = $this->menu->where(Menu::fields_NAME, $menu[Menu::fields_NAME])->find()->fetch();
                    # 保存时检测查询数据，存在则更新
                    if ($menuModel->getId()) {
                        $menu[Menu::fields_ID] = $menuModel->getId();
                    }
                    $menuModel->clearData();
                    $result = $menuModel->setData($menu)->forceCheck(true)->save();
                    # 2 检查自身是否被别的模块作为父分类
                    $menuModel->clearData();
                    if ($this_menu_id = $menuModel->getId() && $is_others_parent = $menuModel->where(Menu::fields_PARENT_SOURCE, $menu[Menu::fields_SOURCE])->select()->fetch()) {
                        foreach ($is_others_parent as $other_menu) {
                            if (empty($other_menu['pid'])) {
                                $other_menu['pid'] = $this_menu_id;
                                $menuModel->forceCheck(false)->setData(Menu::fields_ID, $other_menu['id'])->save($other_menu);
                            }
                        }
                    }
                    unset($menus['data'][$key]);
                }
            }
        }
        # 子菜单
        foreach ($modules_xml_menus as $module => $sub_menus) {
            foreach ($sub_menus['data'] as $menu) {
                # 清空查询条件
                $this->menu->clearData();
                $menu[Menu::fields_MODULE]        = $module;
                $menu[Menu::fields_PARENT_SOURCE] = $menu['parent'] ?? '';
                unset($menu['parent']);
                # 1 存在父资源 检查父资源的 ID
                if (isset($menu[Menu::fields_PARENT_SOURCE]) && $parent = $menu[Menu::fields_PARENT_SOURCE]) {
                    $parent = $this->menu->where(Menu::fields_SOURCE, $parent)->find()->fetch();
                    if ($pid = $parent->getData('id')) {
                        $menu[Menu::fields_PID] = $pid;
                    } else {
                        $menu[Menu::fields_PID] = 0;
                    }
                }
                $menu[Menu::fields_PID] = $menu[Menu::fields_PID] ?? 0;
                $result                 = $menuModel->setData($menu)->forceCheck(true)->save();
                # 2 检查自身是否被别的模块作为父分类
                $menuModel->clearData();
                if ($this_menu_id = $menuModel->getId() && $is_others_parent = $menuModel->where(Menu::fields_PARENT_SOURCE, $menu[Menu::fields_SOURCE])->select()->fetch()) {
                    foreach ($is_others_parent as $other_menu) {
                        if (empty($other_menu['pid'])) {
                            $other_menu['pid'] = $this_menu_id;
                            $menuModel->forceCheck(false)->setData(Menu::fields_ID, $other_menu['id'])->save($other_menu);
                        }
                    }
                }
            }
        }
        # 再次处理父菜单
        $this->menu->clearData();
        $top_menus = $this->menu->where(Menu::fields_PID, 0)->select()->fetch();
        foreach ($top_menus->getItems() as $menu) {
            # 如果存在父菜单，则更新父菜单的id到当前子菜单【pid】
            if ($menu[Menu::fields_PARENT_SOURCE]) {
                # 查找父菜单，获取父菜单的id
                $parent = $this->menu->where(Menu::fields_SOURCE, $menu[Menu::fields_PARENT_SOURCE])->find()->fetch();
                if ($pid = $parent->getData('id')) {
                    $menu[Menu::fields_PID] = $pid;
                    $this->menu->save($menu);
                }
            }
        }
    }
}
