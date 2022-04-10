<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\DeveloperWorkspace\Block;

use Weline\DeveloperWorkspace\Model\Document\Catalog;
use Weline\Framework\Manager\ObjectManager;

class Catalogs extends \Weline\Framework\View\Block
{
    protected string $_template = 'Weline_DeveloperWorkspace::templates/common/left-sidebar.phtml';

    public function __init()
    {
        $this->setCatalogs($this->getCatalogs());
    }

    public function getCatalogs()
    {
        return $this->getCatalog()->getTree();
    }

    private function getCatalog(): Catalog
    {
        return ObjectManager::getInstance(Catalog::class);
    }
}
