<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\DeveloperWorkspace\Controller\Admin;

use Weline\DeveloperWorkspace\Model\Catalog;
use Weline\Framework\Manager\ObjectManager;

class Document extends \Weline\Framework\App\Controller\BackendController
{
    function index()
    {
        /**@var Catalog $catalog */
        $catalog = ObjectManager::getInstance(Catalog::class);
        $catalog = $catalog->pagination(intval($this->_request->getParam('page', 1)),
                                        intval($this->_request->getParam('pageSize', 10)),
                                        $this->_request->getParams())->select()->fetch();
        $this->assign('catalog', $catalog);
        return $this->fetch();
    }
}