<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\Block;

use Weline\Backend\Cache\BackendCache;
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
     * @DESC          # 读取菜单
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/2/9 0:31
     * 参数区：
     * @return mixed
     */
    public function getMenus(): mixed
    {
        return $this->menu->select()->fetch();
    }
}