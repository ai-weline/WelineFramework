<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\UrlManager\Controller\Backend;

use Weline\Framework\Manager\ObjectManager;
use Weline\UrlManager\Model\UrlManager;

class Listing extends \Weline\Framework\App\Controller\BackendController
{
    public function index()
    {
        /**@var UrlManager $urlManager */
        $urlManager = ObjectManager::getInstance(UrlManager::class);
        # 搜索词
        $q = $this->_request->getParam('q');
        if ($q) {
            $urlManager->where('path', "%{$q}%", 'like');
        }
        $urlManager->pagination(
            $this->_request->getParam('page', 1),
            $this->_request->getParam('pageSize', 10),
            $this->_request->getParams()
        )->select()->fetch();
        $this->assign('rewrites', $urlManager->getItems());
        return $this->fetch();
    }
}
