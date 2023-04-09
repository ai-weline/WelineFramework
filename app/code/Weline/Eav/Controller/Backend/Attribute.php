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

    function edit()
    {
        $code      = $this->request->getGet('code');
        $attribute = $this->eavAttribute->loadByCode($code);
        $this->assign('attribute', $attribute);
        $this->init_form();
        return $this->fetch('form');
    }

    function add()
    {
        $this->init_form();
        return $this->fetch('form');
    }

    protected function init_form(){
        /**@var \Weline\Eav\Model\EavAttribute\Type $typeModel*/
        $typeModel = ObjectManager::getInstance(EavAttribute\Type::class);
        $types = $typeModel->select()->fetchOrigin();
        $this->assign('types', $types);
        /**@var Group $grouModel*/
        $groupModel = ObjectManager::getInstance(Group::class);
        $groups = $groupModel->select()->fetchOrigin();
        $this->assign('groups', $groups);
    }
}