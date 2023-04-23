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
        if ($entity = $this->request->getGet('entity')) {
            $this->eavAttribute->where('entity_code', $entity);
        }
        $attributes = $this->eavAttribute->pagination()->select()->fetchOrigin();
        $this->assign('attributes', $attributes);
        $this->assign('pagination', $this->eavAttribute->getPagination());
        return $this->fetch();
    }

    function add()
    {
        if ($this->request->isPost()) {
            $progress      = $this->request->getPost('progress');
            $next_progress = $this->request->getPost('next_progress');
            switch ($progress):
                case 'progress-select-entity':
                    $this->session->setData('entity_code', $this->request->getPost('entity'));
                    $this->assign('progress', $next_progress);
                    break;
                case 'progress-select-set':
                    $this->session->setData('set_code', $this->request->getPost('entity'));
                    $this->assign('progress', $next_progress);
                    break;
                case 'progress-select-group':
                    $this->session->setData('group_code', $this->request->getPost('entity'));
                    $this->assign('progress', $next_progress);
                    break;
                case 'progress-attribute-details':
                    $this->session->setData('attribute', $this->request->getPost());
                    $this->assign('progress', $this->request->getPost('has_option') ? $next_progress : '');
                    break;
                case 'progress-attribute-option':
                    $this->session->setData('attribute_option', $this->request->getPost());
                    $this->assign('progress', '');
                    break;
                default:
                    $entity_code      = $this->session->getData('entity_code');
                    $set_code         = $this->session->getData('set_code');
                    $group_code       = $this->session->getData('group_code');
                    $attribute        = $this->session->getData('attribute');
                    $attribute_option = $this->session->getData('attribute_option');
                    // FIXME 保存数据
            endswitch;
            try {

                /**@var Group $groupModel */
                $groupModel = ObjectManager::getInstance(Group::class);
                $group      = $groupModel->where('code', $group_code)
                                         ->where('entity_code', $entity_code)
                                         ->find()
                                         ->fetch();
                if (!$group->getId()) {
                    $this->getMessageManager()->addWarning(__('分组不在所选属性集内！'));
                    $this->session->setData('attribute', $this->request->getPost());
                    $this->redirect($this->_url->getCurrentUrl());
                }
                $data             = $this->request->getPost();
                $data['set_code'] = $group->getData('set_code');
                $this->eavAttribute->setData($data)
                                   ->save();
                $this->getMessageManager()->addSuccess(__('添加成功！'));
                $this->session->delete('attribute');
            } catch (\Exception $exception) {
                $this->getMessageManager()->addWarning(__('添加异常！'));
                $this->session->setData('attribute', $this->request->getPost());
                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
                $this->redirect('*/backend/attribute/add');
            }
            $this->redirect($this->_url->getBackendUrl('*/backend/attribute/edit', [
                'code'        => $this->request->getPost('code'),
                'entity_code' => $this->request->getPost('entity_code'),
            ]));
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
                $group_code  = $this->request->getPost('group_code', '');
                $entity_code = $this->request->getPost('entity_code', '');
                /**@var Group $groupModel */
                $groupModel = ObjectManager::getInstance(Group::class);
                $group      = $groupModel->where('code', $group_code)
                                         ->where('entity_code', $entity_code)
                                         ->find()
                                         ->fetch();
                if (!$group->getId()) {
                    $this->getMessageManager()->addWarning(__('分组不在所选属性集内！'));
                    $this->session->setData('attribute', $this->request->getPost());
                    $this->redirect($this->_url->getCurrentUrl());
                }
                $data             = $this->request->getPost();
                $data['set_code'] = $group->getData('set_code');
                $this->eavAttribute->setData($data)->save(true);
                $this->getMessageManager()->addSuccess(__('修改成功！'));
                $this->session->delete('attribute');
            } catch (\Exception $exception) {
                $this->getMessageManager()->addWarning(__('修改异常！'));
                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
            }
            $this->redirect('*/backend/attribute/edit', [
                'code'        => $this->request->getPost('code'),
                'entity_code' => $this->request->getPost('entity_code'),
            ]);
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
            $this->assign('attribute', $this->eavAttribute->load('code', $code));
        }
        /**@var \Weline\Eav\Model\EavAttribute\Type $typeModel */
        $typeModel = ObjectManager::getInstance(EavAttribute\Type::class);
        $types     = $typeModel->select()->fetchOrigin();
        $this->assign('types', $types);
        /**@var Group $grouModel */
        $groupModel = ObjectManager::getInstance(Group::class);
        $groups     = $groupModel
            ->joinModel(EavEntity::class, 'entity', 'main_table.entity_code=entity.code', 'left', 'entity.name as entity_name')
            ->select()
            ->fetchOrigin();
        $this->assign('groups', $groups);
        /**@var EavEntity $eavEntityModel */
        $eavEntityModel = ObjectManager::getInstance(EavEntity::class);
        $entities       = $eavEntityModel->select()->fetchOrigin();
        $this->assign('entities', $entities);
        // 链接
        $this->assign('action', $this->_url->getCurrentUrl());
    }
}