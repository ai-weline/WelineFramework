<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/4/9 15:48:17
 */

namespace Weline\Eav\Controller\Backend\Attribute;

use Weline\Eav\Model\EavEntity;
use Weline\Framework\Manager\ObjectManager;

class Set extends \Weline\Framework\App\Controller\BackendController
{
    private \Weline\Eav\Model\EavAttribute\Set $set;

    function __construct(
        \Weline\Eav\Model\EavAttribute\Set $set
    )
    {
        $this->set = $set;
    }

    function index()
    {
        if ($search = $this->request->getGet('search')) {
            $this->set->where('concat(entity,name)', "%$search%", 'like');
        }
        $groups = $this->set->pagination()->select()->fetch()->getItems();
        $this->assign('sets', $groups);
        $this->assign('columns', $this->set->columns());
        $this->assign('pagination', $this->set->getPagination());
        return $this->fetch();
    }


    function add()
    {
        if ($this->request->isPost()) {
            try {
                $this->set->setData($this->request->getPost())
                          ->save(true);
                $this->getMessageManager()->addSuccess(__('添加成功！'));
            } catch (\Exception $exception) {
                $this->getMessageManager()->addWarning(__('添加异常！'));
                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
            }
            $this->redirect($this->_url->getBackendUrl('*/backend/attribute/set/edit', ['code' => $this->request->getPost('code')]));
        }
        $this->init_form();
        return $this->fetch('form');
    }

    function edit()
    {
        if ($this->request->isPost()) {
            try {
                $this->set->setData($this->request->getPost())
                          ->save(true);
                $this->getMessageManager()->addSuccess(__('修改成功！'));
            } catch (\Exception $exception) {
                $this->getMessageManager()->addWarning(__('修改异常！'));
                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
            }
            $this->redirect($this->_url->getBackendUrl('*/backend/attribute/set/edit', ['code' => $this->request->getPost('code')]));
        }
        $this->init_form();
        return $this->fetch('form');
    }

    function getDelete()
    {
        if ($code = $this->request->getGet('code')) {
            $this->set->load('code', $code)->delete();
            $this->getMessageManager()->addSuccess(__('删除成功！'));
        } else {
            $this->getMessageManager()->addError(__('找不到要操作的代码！'));
        }
        $this->redirect($this->_url->getBackendUrl('*/backend/attribute/set'));
    }

    protected function init_form()
    {
        // 属性集
        if ($set_code = $this->request->getGet('code')) {
            $set = $this->set->load('code', $set_code);
            $this->assign('set', $set);
        }
        // 实体
        /**@var \Weline\Eav\Model\EavEntity $eavEntityModel */
        $eavEntityModel = ObjectManager::getInstance(EavEntity::class);
        $entities       = $eavEntityModel->select()->fetchOrigin();
        $this->assign('entities', $entities);
        // 链接
        $this->assign('action', $this->_url->getCurrentUrl());
    }
}