<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/4/9 15:48:02
 */

namespace Weline\Eav\Controller\Backend\Attribute;

use Weline\Eav\Model\EavEntity;
use Weline\Framework\App\Exception;
use Weline\Framework\Exception\Core;
use Weline\Framework\Manager\ObjectManager;

class Group extends \Weline\Framework\App\Controller\BackendController
{
    private \Weline\Eav\Model\EavAttribute\Group $group;

    function __construct(
        \Weline\Eav\Model\EavAttribute\Group $group
    )
    {
        $this->group = $group;
    }

    function index()
    {
        if ($search = $this->request->getGet('search')) {
            $this->group->where('concat(entity,name)', "%$search%", 'like');
        }
        $groups = $this->group->pagination()->select()->fetch()->getItems();
        $this->assign('groups', $groups);
        $this->assign('columns', $this->group->columns());
        $this->assign('pagination', $this->group->getPagination());
        return $this->fetch();
    }

    function getSearch(): string
    {
        $entity_code = $this->request->getGet('entity_code');
        $set_code    = $this->request->getGet('set_code');
        $search      = $this->request->getGet('search');
        $json        = ['items' => [], 'entity_code' => $entity_code, 'search' => $search];
        if (empty($entity_code)) {
            $json['msg'] = __('请先选择实体后操作！');
            return $this->fetchJson($json);
        }
        if (empty($set_code)) {
            $json['msg'] = __('请先选择实体属性集后操作！');
            return $this->fetchJson($json);
        }
        $this->group->where('entity_code', $entity_code);
        if ($search) {
            $this->group->where('concat(name,code) like \'%' . $search . '%\'');
        }
        $groups        = $this->group->select()
                                     ->fetchOrigin();
        $json['items'] = $groups;
        return $this->fetchJson($json);
    }

    function add()
    {
        if ($this->request->isPost()) {
            try {
                $this->validatePost();
                $this->group->setData($this->request->getPost())
                            ->save();
                $this->getMessageManager()->addSuccess(__('添加成功！'));
                $this->session->delete('eav_group');
            } catch (\Exception $exception) {
                $this->getMessageManager()->addWarning(__('添加异常！信息可能已经存在！'));
                $this->session->setData('eav_group', $this->request->getPost());
                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
            }
            $this->redirect($this->_url->getBackendUrl('*/backend/attribute/group/edit', [
                'code'        => $this->request->getPost('code'),
                'entity_code' => $this->request->getPost('entity_code'),
                'set_code'    => $this->request->getPost('set_code'),
            ]));
        }
        $this->init_form();
        return $this->fetch('form');
    }

    function edit()
    {
        if ($this->request->isPost()) {
            try {
                $this->validatePost();
                $this->group->setData($this->request->getPost())
                            ->forceCheck(true, [$this->group::fields_code, $this->group::fields_entity_code, $this->group::fields_set_code])
                            ->save();
                $this->getMessageManager()->addSuccess(__('修改成功！'));
                $this->session->delete('eav_group');
                $this->redirect($this->_url->getBackendUrl('*/backend/attribute/group/edit', [
                    'code'        => $this->request->getPost('code'),
                    'entity_code' => $this->request->getPost('entity_code'),
                    'set_code'    => $this->request->getPost('set_code'),
                ]));
            } catch (\Exception $exception) {
                $this->getMessageManager()->addWarning(__('修改异常！'));
                $this->session->setData('eav_group', $this->request->getPost());
                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
                $this->redirect($this->_url->getCurrentUrl());
            }
        }
        $this->validateGet();
        $this->init_form();
        return $this->fetch('form');
    }

    function getDelete()
    {
        // 属性组
        $code        = $this->request->getGet('code');
        $entity_code = $this->request->getGet('entity_code');
        $set_code    = $this->request->getGet('set_code');
        if ($code && $entity_code && $set_code) {
            $group = $this->group->where('code', $code)
                                 ->where('set_code', $set_code)
                                 ->where('entity_code', $entity_code)
                                 ->find()
                                 ->fetch();
            if (!$group->getId()) {
                $this->getMessageManager()->addError(__('属性组已不存在！'));
            } else {
                try {
                    $this->group->where('code', $code)
                                ->where('set_code', $set_code)
                                ->where('entity_code', $entity_code)
                                ->delete();
                    $this->getMessageManager()->addSuccess(__('删除成功！'));
                } catch (\ReflectionException|Core|Exception $e) {
                    if (DEV) $this->getMessageManager()->addException($e);
                    $this->getMessageManager()->addWarning(__('删除失败！'));
                }
            }
        }
        $this->redirect($this->_url->getBackendUrl('*/backend/attribute/group'));
    }

    protected function init_form()
    {
        if ($eav_group = $this->session->getData('eav_group')) {
            $this->assign('group', $eav_group);
        }
        // 属性组
        $code        = $this->request->getGet('code');
        $entity_code = $this->request->getGet('entity_code');
        $set_code    = $this->request->getGet('set_code');
        if ($code && $entity_code && $set_code) {
            $group = $this->group->where('code', $code)
                                 ->where('set_code', $set_code)
                                 ->where('entity_code', $entity_code)
                                 ->find()
                                 ->fetch();
            $this->assign('group', $group);
        }
        // 实体
        /**@var \Weline\Eav\Model\EavEntity $eavEntityModel */
        $eavEntityModel = ObjectManager::getInstance(EavEntity::class);
        $entities       = $eavEntityModel->select()->fetchOrigin();
        $this->assign('entities', $entities);
        // 链接
        $this->assign('action', $this->_url->getCurrentUrl());
    }

    protected function validateGet(): void
    {
        $code        = $this->request->getGet('code');
        $entity_code = $this->request->getGet('entity_code');
        $set_code    = $this->request->getGet('set_code');
        if (empty($set_code) || empty($code) || empty($entity_code)) {
            $this->getMessageManager()->addWarning(__('参数异常！'));
            $this->session->setData('eav_group', $this->request->getPost());
            $this->redirect($this->_url->getBackendUrl('*/backend/attribute/group'));
        }
    }

    protected function validatePost(): void
    {
        $code        = $this->request->getPost('code');
        $entity_code = $this->request->getPost('entity_code');
        $set_code    = $this->request->getPost('set_code');
        if (empty($set_code) || empty($code) || empty($entity_code)) {
            $this->getMessageManager()->addWarning(__('参数异常！'));
            $this->session->setData('eav_group', $this->request->getPost());
            $this->redirect($this->_url->getCurrentUrl());
        }
    }
}