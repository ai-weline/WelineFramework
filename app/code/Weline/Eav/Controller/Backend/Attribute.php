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
    const        eav_entity                          = 'eav_entity';
    const        eav_entity_attribute_set            = 'eav_entity_attribute_set';
    const        eav_entity_attribute_set_group      = 'eav_entity_attribute_set_group';
    const        eav_attribute                       = 'attribute';
    const        eav_attribute_option                = 'attribute_option';
    const        eav_attribute_add_progress          = 'attribute_add_progress';
    public const add_attribute_progress_session_keys = [
        self::eav_entity                     => self::eav_entity,
        self::eav_entity_attribute_set       => self::eav_entity_attribute_set,
        self::eav_entity_attribute_set_group => self::eav_entity_attribute_set_group,
        self::eav_attribute                  => self::eav_attribute,
        self::eav_attribute_option           => self::eav_attribute_option,
        self::eav_attribute_add_progress     => self::eav_attribute_add_progress,
    ];
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
        /*$this->session->setData('attribute', [
            'code'=>'cost',
            'name'=>'成本'
        ]);*/
        // 卸载session数据
//        foreach (self::add_attribute_progress_session_keys as $add_attribute_progress_session_key) {
//            $this->session->delete($add_attribute_progress_session_key);
//        }
//        $this->session->delete('attribute');
        $this->assign('progress', $this->session->getData('attribute_add_progress'));
        if ($this->request->isPost()) {
            $progress      = $this->request->getPost('progress', '');
            $next_progress = $this->request->getPost('next_progress');
            // 记录当前进度
            $this->session->setData('attribute_add_progress', $progress);
            switch ($progress):
                case 'progress-select-entity':
                    /**@var EavEntity $entityModel */
                    $entityModel = ObjectManager::getInstance(EavEntity::class);
                    $entity      = $entityModel->where('code', $this->request->getPost('entity_code'))->find()->fetch();
                    $this->session->setData(self::eav_entity, $entity->getData());
                    $this->assign('progress', $next_progress);
                    if ($this->request->getGet('isAjax')) {
                        return $this->fetchJson(['code' => 1, 'msg' => __('实体选择成功！')]);
                    }
                    break;
                case 'progress-select-set':
                    if (!isset($this->session->getData('eav_entity')['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择实体！')]);
                        }
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
                    $this->session->setData(self::eav_entity_attribute_set, $entity->getData());
                    $this->assign('progress', $next_progress);
                    if ($this->request->getGet('isAjax')) {
                        return $this->fetchJson(['code' => 1, 'msg' => __('属性集选择成功！')]);
                    }
                    break;
                case 'progress-select-group':
                    if (!isset($this->session->getData('eav_entity')['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择实体！')]);
                        }
                        $this->getMessageManager()->addWarning(__('请先选择实体！'));
                        $this->assign('progress', 'progress-select-entity');
                        break;
                    }
                    if (!isset($this->session->getData('eav_entity_attribute_set')['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择属性集！')]);
                        }
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
                    $this->session->setData(self::eav_entity_attribute_set_group, $group->getData());
                    $this->assign('progress', $next_progress);
                    if ($this->request->getGet('isAjax')) {
                        return $this->fetchJson(['code' => 1, 'msg' => __('属性组选择成功！请继续填写属性数据!')]);
                    }
                    break;
                case 'progress-attribute-details':
                    $this->session->setData(self::eav_attribute, $this->request->getPost());
                    if ($this->request->getGet('isAjax')) {
                        return $this->fetchJson(['code' => 1, 'msg' => __('属性数据填写成功！')]);
                    }
                    $this->assign('progress', 'progress-attribute-details');
                    if ($next_progress !== 'progress_submit') {
                        break;
                    }
                case 'progress-attribute-option':
                    if ($progress === 'progress-attribute-option') {
                        $this->session->setData(self::eav_attribute_option, $this->request->getPost());
                        $this->assign('progress', 'progress-attribute-option');
                    }
                    if ($this->request->getGet('isAjax')) {
                        return $this->fetchJson(['code' => 1, 'msg' => __('属性配置项设置成功！')]);
                    }
                case 'progress_submit':
                default:
                    // 检验
                    if (!isset($this->session->getData(self::eav_entity)['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择实体！')]);
                        }
                        $this->getMessageManager()->addWarning(__('请先选择实体！'));
                        $this->assign('progress', 'progress-select-entity');
                        break;
                    }
                    if (!isset($this->session->getData(self::eav_entity_attribute_set)['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择实体属性集！')]);
                        }
                        $this->getMessageManager()->addWarning(__('请先选择实体属性集！'));
                        $this->assign('progress', 'progress-select-set');
                        break;
                    }
                    if (!isset($this->session->getData(self::eav_entity_attribute_set_group)['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择实体属性组！')]);
                        }
                        $this->getMessageManager()->addWarning(__('请先选择实体属性组！'));
                        $this->assign('progress', 'progress-select-group');
                        break;
                    }
                    if (!isset($this->session->getData(self::eav_attribute)['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先填写属性数据！')]);
                        }
                        $this->getMessageManager()->addWarning(__('请先填写属性数据！'));
                        $this->assign('progress', 'progress-attribute-details');
                        break;
                    }
                    // 如果检测合格则添加
                    $attribute_data['entity_code'] = $this->session->getData(self::eav_entity)['code'];
                    $attribute_data['set_code']    = $this->session->getData(self::eav_entity_attribute_set)['code'];
                    $attribute_data['group_code']  = $this->session->getData(self::eav_entity_attribute_set_group)['code'];
                    // 校验实体-属性集-属性组关系
                    /**@var Group $groupModel */
                    $groupModel = ObjectManager::getInstance(Group::class);
                    $group      = $groupModel->where('code', $attribute_data['group_code'])
                                             ->where('entity_code', $attribute_data['entity_code'])
                                             ->where('set_code', $attribute_data['set_code'])
                                             ->find()
                                             ->fetch();
                    if (!$group->getId()) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('分组不在所选实体属性集内！')]);
                        }
                        $this->getMessageManager()->addWarning(__('分组不在所选实体属性集内！'));
                        $this->redirect($this->_url->getCurrentUrl());
                    }
                    // 合并属性数据
                    if (!$this->session->getData(self::eav_attribute)) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('属性数据不存在！')]);
                        }
                        $this->getMessageManager()->addWarning(__('属性数据不存在！'));
                        $this->redirect($this->_url->getCurrentUrl());
                    }
                    $attribute_data = array_merge($attribute_data, $this->session->getData(self::eav_attribute));

                    try {
                        $this->eavAttribute->clear()
                                           ->setData($attribute_data)
                                           ->save();
                        // 如果属性添加成功，并且有属性配置项，配置属性配置项
                        // 属性配置项
                        if ($this->eavAttribute->getId() && isset($attribute_data['has_option']) && ($attribute_data['has_option'] === '1')) {
                            if (empty($this->session->getData(self::eav_attribute_option))) {
                                if ($this->request->getGet('isAjax')) {
                                    return $this->fetchJson(['code' => 0, 'msg' => __('属性为配置项属性：请设置属性配置项数据！')]);
                                }
                                $this->getMessageManager()->addWarning(__('属性为配置项属性：请设置属性配置项数据！'));
                                $this->redirect($this->_url->getCurrentUrl());
                            }
                            $attribute_options        = $this->session->getData(self::eav_attribute_option);
                            $insert_attribute_options = [];
                            foreach ($attribute_options as $attribute_option) {
                                $insert_attribute_options[] = [
                                    EavAttribute\Option::fields_CODE           => $attribute_option['code'],
                                    EavAttribute\Option::fields_name           => $attribute_option['name'],
                                    EavAttribute\Option::fields_entity_code    => $attribute_data['entity_code'],
                                    EavAttribute\Option::fields_attribute_code => $attribute_data['code'],
                                ];
                            }
                            /**@var \Weline\Eav\Model\EavAttribute\Option $optionModel */
                            $optionModel = ObjectManager::getInstance(EavAttribute\Option::class);
                            $optionModel->insert($insert_attribute_options, [EavAttribute\Option::fields_CODE,
                                                                             EavAttribute\Option::fields_entity_code, EavAttribute\Option::fields_attribute_code]);
                        }
                        $this->getMessageManager()->addSuccess(__('添加成功！'));
                        // 卸载session数据
                        foreach (self::add_attribute_progress_session_keys as $add_attribute_progress_session_key) {
                            $this->session->delete($add_attribute_progress_session_key);
                        }
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 1, 'msg' => __('添加成功！')]);
                        }
                    } catch (\Exception $exception) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('添加异常！')]);
                        }
                        $this->getMessageManager()->addWarning(__('添加异常！'));
                        if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
                        $this->redirect('*/backend/attribute/add');
                    }
                    $this->redirect($this->_url->getBackendUrl('*/backend/attribute/edit', [
                        'code'        => $this->request->getPost('code'),
                        'entity_code' => $this->request->getPost('entity_code'),
                    ]));
                    break;
            endswitch;
            // 记录进度
            $this->session->setData(self::eav_attribute_add_progress, $progress);
        }
        // 装配session记录数据
        if ($data = $this->session->getData(self::eav_entity)) {
            $this->assign(self::eav_entity, $data);
        }
        if ($data = $this->session->getData(self::eav_entity_attribute_set)) {
            $this->assign(self::eav_entity_attribute_set, $data);
        }
        if ($data = $this->session->getData(self::eav_entity_attribute_set_group)) {
            $this->assign(self::eav_entity_attribute_set_group, $data);
        }
        if ($data = $this->session->getData(self::eav_attribute)) {
            $this->assign(self::eav_attribute, $data);
        }
        if ($data = $this->session->getData(self::eav_attribute_option)) {
            $this->assign(self::eav_attribute_option, $data);
        }
        $entity_code = $this->session->getData('eav_entity')['code'] ?? '';
        $attribute   = $this->session->getData(self::eav_attribute) ?: [];
        // 检测如果有has_option则添加options
        $has_option     = $attribute['has_option'];
        $attribute_code = $attribute['code'];
        if ($has_option === '1' && ($entity_code && $attribute_code)) {
            /**@var \Weline\Eav\Model\EavAttribute\Option $optionModel */
            $optionModel = ObjectManager::getInstance(EavAttribute\Option::class);
            $options     = $optionModel->where([
                                                   'entity_code'    => $entity_code,
                                                   'attribute_code' => $this->request->getPost('code')
                                               ])
                                       ->select()->fetchOrigin();
            $this->assign('options', $options);
        }
        $this->init_form();
        return $this->fetch('form');
    }

    function edit()
    {
        /*$this->session->setData('attribute', [
           'code'=>'cost',
           'name'=>'成本'
       ]);*/
        // 卸载session数据
//        foreach (self::add_attribute_progress_session_keys as $add_attribute_progress_session_key) {
//            $this->session->delete($add_attribute_progress_session_key);
//        }
//        $this->session->delete('attribute');
        $this->assign('progress', $this->session->getData('attribute_add_progress'));
        if ($this->request->isPost()) {
            $progress      = $this->request->getPost('progress', '');
            $next_progress = $this->request->getPost('next_progress');
            // 记录当前进度
            $this->session->setData('attribute_add_progress', $progress);
            switch ($progress):
                case 'progress-select-entity':
                    /**@var EavEntity $entityModel */
                    $entityModel = ObjectManager::getInstance(EavEntity::class);
                    $entity      = $entityModel->where('code', $this->request->getPost('entity_code'))->find()->fetch();
                    $this->session->setData(self::eav_entity, $entity->getData());
                    $this->assign('progress', $next_progress);
                    if ($this->request->getGet('isAjax')) {
                        return $this->fetchJson(['code' => 1, 'msg' => __('实体选择成功！')]);
                    }
                    break;
                case 'progress-select-set':
                    if (!isset($this->session->getData('eav_entity')['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择实体！')]);
                        }
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
                    $this->session->setData(self::eav_entity_attribute_set, $entity->getData());
                    $this->assign('progress', $next_progress);
                    if ($this->request->getGet('isAjax')) {
                        return $this->fetchJson(['code' => 1, 'msg' => __('属性集选择成功！')]);
                    }
                    break;
                case 'progress-select-group':
                    if (!isset($this->session->getData('eav_entity')['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择实体！')]);
                        }
                        $this->getMessageManager()->addWarning(__('请先选择实体！'));
                        $this->assign('progress', 'progress-select-entity');
                        break;
                    }
                    if (!isset($this->session->getData('eav_entity_attribute_set')['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择属性集！')]);
                        }
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
                    $this->session->setData(self::eav_entity_attribute_set_group, $group->getData());
                    $this->assign('progress', $next_progress);
                    if ($this->request->getGet('isAjax')) {
                        return $this->fetchJson(['code' => 1, 'msg' => __('属性组选择成功！请继续填写属性数据!')]);
                    }
                    break;
                case 'progress-attribute-details':
                    $this->session->setData(self::eav_attribute, $this->request->getPost());
                    $this->assign('progress', 'progress-attribute-details');
                    if ($this->request->getGet('isAjax')) {
                        return $this->fetchJson(['code' => 1, 'msg' => __('属性数据填写成功！')]);
                    }
                    if ($next_progress !== 'progress_submit') {
                        break;
                    }
                case 'progress-attribute-option':
                    if ($progress === 'progress-attribute-option') {
                        $this->session->setData(self::eav_attribute_option, $this->request->getPost());
                        $this->assign('progress', 'progress-attribute-option');
                    }
                    if ($this->request->getGet('isAjax')) {
                        return $this->fetchJson(['code' => 1, 'msg' => __('属性配置项设置成功！')]);
                    }
                case 'progress_submit':
                default:
                    // 检验
                    if (!isset($this->session->getData(self::eav_entity)['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择实体！')]);
                        }
                        $this->getMessageManager()->addWarning(__('请先选择实体！'));
                        $this->assign('progress', 'progress-select-entity');
                        break;
                    }
                    if (!isset($this->session->getData(self::eav_entity_attribute_set)['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择实体属性集！')]);
                        }
                        $this->getMessageManager()->addWarning(__('请先选择实体属性集！'));
                        $this->assign('progress', 'progress-select-set');
                        break;
                    }
                    if (!isset($this->session->getData(self::eav_entity_attribute_set_group)['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先选择实体属性组！')]);
                        }
                        $this->getMessageManager()->addWarning(__('请先选择实体属性组！'));
                        $this->assign('progress', 'progress-select-group');
                        break;
                    }
                    if (!isset($this->session->getData(self::eav_attribute)['code'])) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('请先填写属性数据！')]);
                        }
                        $this->getMessageManager()->addWarning(__('请先填写属性数据！'));
                        $this->assign('progress', 'progress-attribute-details');
                        break;
                    }
                    // 如果检测合格则添加
                    $attribute_data['entity_code'] = $this->session->getData(self::eav_entity)['code'];
                    $attribute_data['set_code']    = $this->session->getData(self::eav_entity_attribute_set)['code'];
                    $attribute_data['group_code']  = $this->session->getData(self::eav_entity_attribute_set_group)['code'];
                    // 校验实体-属性集-属性组关系
                    /**@var Group $groupModel */
                    $groupModel = ObjectManager::getInstance(Group::class);
                    $group      = $groupModel->where('code', $attribute_data['group_code'])
                                             ->where('entity_code', $attribute_data['entity_code'])
                                             ->where('set_code', $attribute_data['set_code'])
                                             ->find()
                                             ->fetch();
                    if (!$group->getId()) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('分组不在所选实体属性集内！')]);
                        }
                        $this->getMessageManager()->addWarning(__('分组不在所选实体属性集内！'));
                        $this->redirect($this->_url->getCurrentUrl());
                    }
                    // 合并属性数据
                    if (!$this->session->getData(self::eav_attribute)) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('属性数据不存在！')]);
                        }
                        $this->getMessageManager()->addWarning(__('属性数据不存在！'));
                        $this->redirect($this->_url->getCurrentUrl());
                    }
                    $attribute_data = array_merge($attribute_data, $this->session->getData(self::eav_attribute));

                    try {
                        $this->eavAttribute->clear()
                                           ->setData($attribute_data)
                                           ->save();
                        // 如果属性添加成功，并且有属性配置项，配置属性配置项
                        // 属性配置项
                        if ($this->eavAttribute->getId() && isset($attribute_data['has_option']) && ($attribute_data['has_option'] === '1')) {
                            if (empty($this->session->getData(self::eav_attribute_option))) {
                                if ($this->request->getGet('isAjax')) {
                                    return $this->fetchJson(['code' => 0, 'msg' => __('属性为配置项属性：请设置属性配置项数据！')]);
                                }
                                $this->getMessageManager()->addWarning(__('属性为配置项属性：请设置属性配置项数据！'));
                                $this->redirect($this->_url->getCurrentUrl());
                            }
                            $attribute_options        = $this->session->getData(self::eav_attribute_option);
                            $insert_attribute_options = [];
                            foreach ($attribute_options as $attribute_option) {
                                $insert_attribute_options[] = [
                                    EavAttribute\Option::fields_CODE           => $attribute_option['code'],
                                    EavAttribute\Option::fields_name           => $attribute_option['name'],
                                    EavAttribute\Option::fields_entity_code    => $attribute_data['entity_code'],
                                    EavAttribute\Option::fields_attribute_code => $attribute_data['code'],
                                ];
                            }
                            /**@var \Weline\Eav\Model\EavAttribute\Option $optionModel */
                            $optionModel = ObjectManager::getInstance(EavAttribute\Option::class);
                            $optionModel->insert($insert_attribute_options, [EavAttribute\Option::fields_CODE,
                                                                             EavAttribute\Option::fields_entity_code, EavAttribute\Option::fields_attribute_code]);
                        }
                        $this->getMessageManager()->addSuccess(__('添加成功！'));
                        // 卸载session数据
                        foreach (self::add_attribute_progress_session_keys as $add_attribute_progress_session_key) {
                            $this->session->delete($add_attribute_progress_session_key);
                        }
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 1, 'msg' => __('添加成功！')]);
                        }
                    } catch (\Exception $exception) {
                        if ($this->request->getGet('isAjax')) {
                            return $this->fetchJson(['code' => 0, 'msg' => __('添加异常！')]);
                        }
                        $this->getMessageManager()->addWarning(__('添加异常！'));
                        if (DEBUG || DEV) $this->getMessageManager()->addException($exception);
                        $this->redirect('*/backend/attribute/add');
                    }
                    $this->redirect($this->_url->getBackendUrl('*/backend/attribute/edit', [
                        'code'        => $this->request->getPost('code'),
                        'entity_code' => $this->request->getPost('entity_code'),
                    ]));
                    break;
            endswitch;
            // 记录进度
            $this->session->setData(self::eav_attribute_add_progress, $progress);
        }
        // 装配session记录数据
        if ($data = $this->session->getData(self::eav_entity)) {
            $this->assign(self::eav_entity, $data);
        }
        if ($data = $this->session->getData(self::eav_entity_attribute_set)) {
            $this->assign(self::eav_entity_attribute_set, $data);
        }
        if ($data = $this->session->getData(self::eav_entity_attribute_set_group)) {
            $this->assign(self::eav_entity_attribute_set_group, $data);
        }
        if ($data = $this->session->getData(self::eav_attribute)) {
            $this->assign(self::eav_attribute, $data);
        }
        if ($data = $this->session->getData(self::eav_attribute_option)) {
            $this->assign(self::eav_attribute_option, $data);
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

        /**@var EavEntity $eavEntityModel */
        $eavEntityModel = ObjectManager::getInstance(EavEntity::class);
        $entities       = $eavEntityModel->select()->fetchOrigin();
        $this->assign('entities', $entities);

        $entity_code = $this->session->getData(self::eav_entity)['code'] ?? '';
        if ($entity_code) {
            /**@var \Weline\Eav\Model\EavAttribute\Set $setModel */
            $setModel = ObjectManager::getInstance(EavAttribute\Set::class);
            $sets     = $setModel
                ->where('main_table.entity_code', $entity_code)
                ->joinModel(EavEntity::class, 'entity', 'main_table.entity_code=entity.code', 'left', 'entity.name as entity_name')
                ->select()
                ->fetchOrigin();
            $this->assign('sets', $sets);
        }
        $set_code = $this->session->getData(self::eav_entity_attribute_set)['code'] ?? '';
        if ($set_code) {
            /**@var Group $grouModel */
            $groupModel = ObjectManager::getInstance(Group::class);
            $groups     = $groupModel
                ->where('main_table.entity_code', $entity_code)
                ->where('main_table.set_code', $set_code)
                ->joinModel(EavEntity::class, 'entity', 'main_table.entity_code=entity.code', 'left', 'entity.name as entity_name')
                ->select()
                ->fetchOrigin();
            $this->assign('groups', $groups);
        }
        // 链接
        $this->assign('action', $this->_url->getCurrentUrl());
    }
}