<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/6 21:52:16
 */

namespace Weline\Eav\Observer;

use Weline\Eav\Model\Attribute;
use Weline\Eav\Model\Entity;
use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Event\Event;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Module\Config\ModuleFileReader;

class UpgradeEav implements \Weline\Framework\Event\ObserverInterface
{
    /**
     * @var \Weline\Framework\Module\Config\ModuleFileReader
     */
    private ModuleFileReader $moduleFileReader;
    /**
     * @var \Weline\Eav\Model\Entity
     */
    private Entity $entity;
    /**
     * @var \Weline\Eav\Model\Attribute
     */
    private Attribute $attribute;

    function __construct(
        ModuleFileReader $moduleFileReader,
        Entity           $entity,
        Attribute        $attribute
    )
    {
        $this->moduleFileReader = $moduleFileReader;
        $this->entity           = $entity;
        $this->attribute        = $attribute;
    }

    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        $modules = Env::getInstance()->getActiveModules();
        $eavs    = [];
        foreach ($modules as $module) {
            $eavs = array_merge($eavs, $this->moduleFileReader->readClass($module['base_path'], 'Eav'));
        }
        foreach ($eavs as $eav) {
            /**@var \Weline\Eav\EavInterface $eavEntity */
            $eavEntity = ObjectManager::getInstance($eav);
            if (empty($eavEntity->getEntityCode())) {
                throw new Exception(__('实体没有代码：entity_code,涉及实体类：%1', $eav));
            }
            if (empty($eavEntity->getEntityName())) {
                throw new Exception(__('实体没有名称：entity_name,涉及实体类：%1', $eav));
            }
            $this->entity->clear()
                         ->setData(
                             [
                                 $this->entity::fields_ID    => $eavEntity->getEntityCode(),
                                 $this->entity::fields_class => $eav,
                                 $this->entity::fields_name  => $eavEntity->getEntityName(),
                             ]
                         )
                         ->save(true);
        }
    }
}