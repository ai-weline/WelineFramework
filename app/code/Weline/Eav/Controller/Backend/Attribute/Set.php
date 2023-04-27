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
                $this->validatePost();
                $this->set->setData($this->request->getPost())
                          ->save();
                $this->getMessageManager()->addSuccess(__('添加成功！'));
                $this->session->delete('eav_set');
                $this->redirect($this->_url->getBackendUrl('*/backend/attribute/set/edit',
                                                           [
                                                               'code'        => $this->request->getPost('code'),
                                                               'entity_code' => $this->request->getPost('entity_code')
                                                           ]));
            } catch (\Exception $exception) {
                $this->getMessageManager()->addWarning(__('添加异常！可能已存在该属性集！'));
                $this->session->setData('eav_set', $this->request->getPost());
                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
                $this->redirect($this->_url->getCurrentUrl());
            }
        }
        if ($set = $this->session->getData('eav_set')) {
            $this->assign('set', $set);
        }
        $this->init_form();
        return $this->fetch('form');
    }

    function edit()
    {
        if ($this->request->isPost()) {
            try {
                $this->validatePost();
                $this->set->setData($this->request->getPost())
                          ->forceCheck(true, [$this->set::fields_code, $this->set::fields_entity_code])
                          ->save();
                $this->getMessageManager()->addSuccess(__('修改成功！'));
                $this->session->delete('eav_set');
            } catch (\Exception $exception) {
                $this->getMessageManager()->addWarning(__('修改异常！'));
                $this->session->setData('eav_set', $this->request->getPost());
                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
            }
            $this->redirect($this->_url->getBackendUrl('*/backend/attribute/set/edit', ['code' => $this->request->getPost('code')]));
        }
        $this->validateGet();
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

    function getApiSearch(): string
    {
        $entity_code = $this->request->getGet('entity_code');
        $json        = ['items' => [], 'entity_code' => $entity_code];
        if (empty($entity_code)) {
            return $this->fetchJson($json);
        }
        $sets          = $this->set->where('entity_code', $entity_code)
                                   ->select()
                                   ->fetchOrigin();
        $json['items'] = $sets;
        return $this->fetchJson($json);
    }

    function getSearch(): string
    {
        $entity_code = $this->request->getGet('entity_code');
        $search      = $this->request->getGet('search');
        $json        = ['items' => [], 'entity_code' => $entity_code, 'search' => $search];
        if (empty($entity_code)) {
            $json['msg'] = __('请先选择实体后操作！');
            return $this->fetchJson($json);
        }
        $this->set->where('entity_code', $entity_code);
        if ($search) {
            $this->set->where('concat(name,code) like \'%' . $search . '%\'');
        }
        $sets          = $this->set->select()
                                   ->fetchOrigin();
        $json['items'] = $sets;
        return $this->fetchJson($json);
    }

    protected function validateGet(): void
    {
        $code        = $this->request->getGet('code');
        $entity_code = $this->request->getGet('entity_code');
        if (empty($code) || empty($entity_code)) {
            $this->getMessageManager()->addWarning(__('参数异常！'));
            $this->session->setData('eav_set', $this->request->getPost());
            $this->redirect($this->_url->getBackendUrl('*/backend/attribute/set'));
        }
    }

    protected function validatePost(): void
    {
        $code        = $this->request->getPost('code');
        $entity_code = $this->request->getPost('entity_code');
        if (empty($code) || empty($entity_code)) {
            $this->getMessageManager()->addWarning(__('参数异常！'));
            $this->session->setData('eav_set', $this->request->getPost());
            $this->redirect($this->_url->getCurrentUrl());
        }
    }

    protected function init_form()
    {
        // 属性集
        if ($set_code = $this->request->getGet('code')) {
            $set = $this->set->load('code', $set_code);
            $this->assign('set', $set);
        }
        if ($set = $this->session->getData('eav_set')) {
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