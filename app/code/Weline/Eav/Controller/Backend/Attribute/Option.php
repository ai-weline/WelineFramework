<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/24 22:44:09
 */

namespace Weline\Eav\Controller\Backend\Attribute;

use Weline\Eav\Model\EavAttribute;
use Weline\Framework\App\Controller\BackendController;
use Weline\Framework\Manager\ObjectManager;

class Option extends BackendController
{
    public function search()
    {
        /**@var \Weline\Eav\Model\EavAttribute\Option $optionModel */
        $optionModel = ObjectManager::getInstance(EavAttribute\Option::class);
        if ($search = $this->request->getGet('search')) {
            $optionModel->where('concat(`attribute`,`name`,`entity`,`option`)', "%{$search}%", 'like');
        }
        $entity_code    = $this->request->getGet('entity_code');
        $attribute_code = $this->request->getGet('attribute_code');
        if (empty($entity_code) || empty($attribute_code)) {
            $this->redirect(403);
        }
        $optionModel->where('entity_code', $entity_code)
                    ->where('attribute_code', $attribute_code);
        $options = $optionModel->pagination()->select()->fetchOrigin();

        return $this->fetchJson([
                                    'options'    => $options,
                                    'pagination' => $optionModel->getPaginationData(),
                                    'params'     => $this->request->getGet()
                                ]);
    }

    // FIXME 持续完成配置项
    function getForm()
    {

    }

    function postForm()
    {
        // 检测属性
        $attribute = ObjectManager::getInstance(EavAttribute::class)->load('code', $this->request->getPost('attribute'));
        if (!$attribute->getId()) {
            $this->getMessageManager()->addWarning(__('属性已不存在！'));
            $this->redirect('*/backend/attribute/option');
        }
        /**@var \Weline\Eav\Model\EavAttribute\Option $optionModel */
        $optionModel = ObjectManager::getInstance(EavAttribute\Option::class);
        try {
            $result = $optionModel->setData($this->request->getPost())
                                  ->forceCheck(true,
                                               [EavAttribute\Option::fields_attribute, EavAttribute\Option::fields_option]
                                  )->save();
            $this->getMessageManager()->addSuccess(__('添加配置项成功！'));
        } catch (\Exception $exception) {
            $this->getMessageManager()->addWarning(__('添加配置项失败！'));
            if (DEV) {
                $this->getMessageManager()->addException($exception);
            }
            $this->session->setData('eav_attribute_option', $this->request->getPost());
        }
    }
}