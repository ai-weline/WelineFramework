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

    function getSearch(): string
    {
        $field       = $this->request->getGet('field');
        $limit       = $this->request->getGet('limit');
        $entity_code = $this->request->getGet('entity_code');
        $set_code    = $this->request->getGet('set_code');
        $group_code  = $this->request->getGet('group_code');
        $search      = $this->request->getGet('search');
        $json        = ['items' => [], 'entity_code' => $entity_code, 'set_code' => $set_code, 'group_code' => $group_code, 'limit' => $limit, 'search'
                                =>
            $search];
        if (empty($entity_code)) {
            $json['msg'] = __('请先选择实体后操作！');
            return $this->fetchJson($json);
        }
        if (empty($set_code)) {
            $json['msg'] = __('请先选择属性集后操作！');
            return $this->fetchJson($json);
        }
        if (empty($group_code)) {
            $json['msg'] = __('请先选择属性组后操作！');
            return $this->fetchJson($json);
        }
        $this->eavAttribute->where('entity_code', $entity_code)
                           ->where('set_code', $set_code)
                           ->where('group_code', $group_code);
        if ($field && $search) {
            $this->eavAttribute->where($field, $search);
            if ($limit) {
                $this->eavAttribute->limit(1);
            } else {
                $this->eavAttribute->limit(100);
            }
        } else {
            return $this->fetchJson($json);
        }
        $attributes    = $this->eavAttribute->select()
                                            ->fetchOrigin();
        $json['items'] = $attributes;
        return $this->fetchJson($json);
    }

    function add()
    {
        $this->assign('progress', $this->session->getData('attribute_add_progress'));
        if ($this->request->isPost()) {
            $progress      = $this->request->getPost('progress', '');
            $next_progress = $this->request->getPost('next_progress');
            // 记录当前进度
            $this->session->setData('attribute_add_progress', $progress);
            switch ($progress):
                case 'progress-select-entity':
                    // FIXME 如果重新选择不一样的实体，清空后续的记录
                    /**@var EavEntity $entityModel */
                    $entityModel = ObjectManager::getInstance(EavEntity::class);
                    $entity      = $entityModel->where('code', $this->request->getPost('entity_code'))->find()->fetch();
                    $this->session->setData('eav_entity', $entity->getData());
                    $this->assign('progress', $next_progress);
                    break;
                case 'progress-select-set':
                    if (!isset($this->session->getData('eav_entity')['code'])) {
                        $this->getMessageManager()->addWarning(__('请先选择实体！'));
                        $this->assign('progress', 'progress-select-entity');
                        break;
                    }
                    /**@var EavAttribute\Set $setModel */
                    $setModel = ObjectManager::getInstance(EavAttribute\Set::class);
                    $entity   = $setModel->where('code', $this->request->getPost('set_code'))
                                         ->where('entity_code', $this->session->getData('eav_entity')['code'])
                                         ->find()
                                         ->fetch();
                    $this->session->setData('eav_entity_attribute_set', $entity->getData());
                    $this->assign('progress', $next_progress);
                    break;
                case 'progress-select-group':
                    if (!isset($this->session->getData('eav_entity')['code'])) {
                        $this->getMessageManager()->addWarning(__('请先选择实体！'));
                        $this->assign('progress', 'progress-select-entity');
                        break;
                    }
                    if (!isset($this->session->getData('eav_entity_attribute_set')['code'])) {
                        $this->getMessageManager()->addWarning(__('请先选择属性集！'));
                        $this->assign('progress', 'progress-select-set');
                        break;
                    }
                    /**@var EavAttribute\Group $groupModel */
                    $groupModel = ObjectManager::getInstance(EavAttribute\Group::class);
                    $group      = $groupModel->where('code', $this->request->getPost('group_code'))
                                             ->where('entity_code', $this->session->getData('eav_entity')['code'])
                                             ->where('set_code', $this->session->getData('eav_entity_attribute_set')['code'])
                                             ->find()
                                             ->fetch();
                    $this->session->setData('eav_entity_attribute_set_group', $group->getData());
                    $this->assign('progress', $next_progress);
                    break;
                case 'progress-attribute-details':
                    $this->session->setData('attribute', $this->request->getPost());
                    $this->assign('progress', $this->request->getPost('has_option') ? $next_progress : 'progress-attribute-details');
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
//            try {
//
//                /**@var Group $groupModel */
//                $groupModel = ObjectManager::getInstance(Group::class);
//                $group      = $groupModel->where('code', $group_code)
//                                         ->where('entity_code', $entity_code)
//                                         ->find()
//                                         ->fetch();
//                if (!$group->getId()) {
//                    $this->getMessageManager()->addWarning(__('分组不在所选属性集内！'));
//                    $this->session->setData('attribute', $this->request->getPost());
//                    $this->redirect($this->_url->getCurrentUrl());
//                }
//                $data             = $this->request->getPost();
//                $data['set_code'] = $group->getData('set_code');
//                $this->eavAttribute->setData($data)
//                                   ->save();
//                $this->getMessageManager()->addSuccess(__('添加成功！'));
//                $this->session->delete('attribute');
//            } catch (\Exception $exception) {
//                $this->getMessageManager()->addWarning(__('添加异常！'));
//                $this->session->setData('attribute', $this->request->getPost());
//                if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
//                $this->redirect('*/backend/attribute/add');
//            }
//            $this->redirect($this->_url->getBackendUrl('*/backend/attribute/edit', [
//                'code'        => $this->request->getPost('code'),
//                'entity_code' => $this->request->getPost('entity_code'),
//            ]));
            // 记录进度
            $this->session->setData('attribute_add_progress', $progress);
        }
        if ($data = $this->session->getData('attribute')) {
            $this->assign('attribute', $data);
        }
        if ($data = $this->session->getData('eav_entity')) {
            $this->assign('eav_entity', $data);
        }
        if ($data = $this->session->getData('eav_entity_attribute_set')) {
            $this->assign('eav_entity_attribute_set', $data);
        }
        if ($data = $this->session->getData('eav_entity_attribute_set_group')) {
            $this->assign('eav_entity_attribute_set_group', $data);
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