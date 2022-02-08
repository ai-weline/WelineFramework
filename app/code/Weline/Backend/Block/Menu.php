<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\Block;

use Weline\Framework\View\Block;

class Menu extends Block
{
    private \Weline\Backend\Model\Menu $menu;

    function __construct(
        \Weline\Backend\Model\Menu $menu,
        array                      $data = []
    )
    {
        parent::__construct($data);
        $this->menu = $menu;
    }

    /*function __init(){
        $this->setTemplate('Weline_Backend::blocks/menu.phtml');
    }*/
    /**
     * @DESC          # 方法描述
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/11/16 23:22
     * 参数区：
     */
    function getMenus()
    {
        p($this->menu->joinModel($this->menu, 't','t.pid=main_table.id')->select()->fetchOrigin());
        return $this->menu->joinModel($this->menu, 't','t.pid=main_table.id')->select()->fetch();
    }
}