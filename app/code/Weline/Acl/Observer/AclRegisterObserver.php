<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/1/5 00:30:21
 */

namespace Weline\Acl\Observer;

use Weline\Framework\Event\Event;
use Weline\Framework\Manager\ObjectManager;

class AclRegisterObserver implements \Weline\Framework\Event\ObserverInterface
{

    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        /**@var \Weline\Framework\Acl\Acl $acl */
        $acl = $event->getData('data');
        /**@var \Weline\Acl\Model\Acl $alcModel */
        $alcModel = ObjectManager::getInstance(\Weline\Acl\Model\Acl::class);
        $alcModel->insert(
            $acl->getData(),
            [
                $alcModel::fields_SOURCE_NAME,
                $alcModel::fields_ROUTE,
                $alcModel::fields_METHOD,
                $alcModel::fields_MODULE,
                $alcModel::fields_REWRITE,
                $alcModel::fields_ROUTER,
                $alcModel::fields_DOCUMENT,
                $alcModel::fields_PARENT_SOURCE,
                $alcModel::fields_CLASS,
            ])
            ->fetch();
    }
}