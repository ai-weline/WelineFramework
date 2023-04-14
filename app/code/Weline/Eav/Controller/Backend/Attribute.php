<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/22 21:40:54
 */

namespace Weline\Eav\Controller\Backend;

use Weline\Eav\Model\EavAttribute;
use Weline\Eav\Model\EavAttribute\Group;
use Weline\Eav\Model\EavEntity;
use Weline\Framework\Manager\ObjectManager;

class Attribute extends \Weline\Framework\App\Controller\BackendController
{
    /**
     * @var \Weline\Eav\Model\EavAttribute
     */
    private EavAttribute $eavAttribute;

    function __construct(EavAttribute $eavAttribute)
    {
        $this->eavAttribute = $eavAttribute;
    }

    function index()
    {
        if ($search = $this->request->getGet('search')) {
            $this->eavAttribute->where('concat(code,entity,name,type)', "%$search%", 'like');
        }
        $attributes = $this->eavAttribute->pagination()->select()->fetchOrigin();
        $this->assign('attributes', $attributes);
        $this->assign('pagination', $this->eavAttribute->getPagination());
        return $this->fetch();
    }

    function add()
    {
        // FIXME 只需要分组信息即可
        if ($this->request->isPost()) {
            try {
                $group_id = $this->request->getPost('group_id');
                $set_id   = $this->request->getPost('set_id');
                /**@var Group $groupModel */
                $groupModel = ObjectManager::getInstance(Group::class);
                $group      = $groupModel->where('set_id', $set_id)->where('group_id', $group_id)->find()->fetchOrigin();
                if (!$group) {
                    $this->getMessageManager()->addWarning(__('分组不在所选属性集内！'));
                    $this->session->setData('attribute', $this->request->getPost());
                    $this->redirect($this->_url->getCurrentUrl());
                }
                $this->eavAttribute->setData($this->request->getPost())
                                   ->save(true);
                $this->getMessageManager()->addSuccess(__('添加成功！'));
                $this->session->delete('attribute');
            } catch (\Exception $exception) {
                $this->getMessageManager()->addWarning(__('添加异常！'));
                $this->session->setData('attribute', $this->request->getPost());
                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
            }
            $this->redirect($this->_url->getBackendUrl('*/backend/attribute/edit', ['code' => $this->request->getPost('code')]));
        }
        if ($data = $this->session->getData('attribute')) {
            $this->assign('attribute', $data);
        }
        $this->init_form();
        return $this->fetch('form');
    }

    function edit()
    {
        if ($this->request->isPost()) {
            try {
                $group_id = $this->request->getPost('group_id',0);
                $set_id   = $this->request->getPost('set_id',0);
                /**@var Group $groupModel */
                $groupModel = ObjectManager::getInstance(Group::class);
                $group      = $groupModel->where('set_id', $set_id)->where('group_id', $group_id)->find()->fetchOrigin();
                if (!$group) {
                    $this->getMessageManager()->addWarning(__('分组不在所选属性集内！'));
                    $this->session->setData('attribute', $this->request->getPost());
                    $this->redirect($this->_url->getCurrentUrl());
                }
                $this->eavAttribute->setData($this->request->getPost())
                                   ->save(true);
                $this->getMessageManager()->addSuccess(__('修改成功！'));
                $this->session->delete('attribute');
            } catch (\Exception $exception) {
                $this->getMessageManager()->addWarning(__('修改异常！'));
                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
            }
            $this->redirect($this->_url->getBackendUrl('*/backend/attribute/edit', ['code' => $this->request->getPost('code')]));
        }
        $this->init_form();
        return $this->fetch('form');
    }

    function getDelete()
    {
        if ($code = $this->request->getGet('code')) {
            $this->eavAttribute->load('code', $code)->delete();
            $this->getMessageManager()->addSuccess(__('删除成功！'));
        } else {
            $this->getMessageManager()->addError(__('找不到要操作的代码！'));
        }
        $this->redirect($this->_url->getBackendUrl('*/backend/attribute'));
    }

    protected function init_form()
    {
        if ($code = $this->request->getGet('code')) {
            $this->assign('attribute', $this->eavAttribute->load('code',$code));
        }
        /**@var \Weline\Eav\Model\EavAttribute\Type $typeModel */
        $typeModel = ObjectManager::getInstance(EavAttribute\Type::class);
        $types     = $typeModel->select()->fetchOrigin();
        $this->assign('types', $types);
        /**@var Group $grouModel */
        $groupModel = ObjectManager::getInstance(Group::class);
        $groups     = $groupModel->select()->fetchOrigin();
        $this->assign('groups', $groups);
        /**@var EavAttribute\Set $setModel */
        $setModel = ObjectManager::getInstance(EavAttribute\Set::class);
        $sets     = $setModel->select()->fetchOrigin();
        $this->assign('sets', $sets);
        /**@var EavEntity $eavEntityModel */
        $eavEntityModel = ObjectManager::getInstance(EavEntity::class);
        $entities     = $eavEntityModel->select()->fetchOrigin();
        $this->assign('entities', $entities);
        // 链接
        $this->assign('action', $this->_url->getCurrentUrl());
    }
}