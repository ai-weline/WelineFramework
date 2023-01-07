<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/1/7 22:14:08
 */

namespace Weline\Acl\Observer;

use Weline\Acl\Model\Acl;
use Weline\Framework\Event\Event;
use Weline\Framework\Manager\ObjectManager;

class ControllerMethodAttributes implements \Weline\Framework\Event\ObserverInterface
{
    /**
     * @var \Weline\Acl\Model\Acl
     */
    private Acl $acl;

    function __construct(
        Acl $acl
    ){
        $this->acl = $acl;
    }
    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        $type = $event->getData('type');
        /**@var \Weline\Framework\DataObject\DataObject $data */
        $data = $event->getData('data');
        /**@var \ReflectionAttribute $attribute */
        $attribute = $event->getData('attribute');
        // Acl属性
        if($attribute->getName()===\Weline\Framework\Acl\Acl::class){
            /**@var \Weline\Framework\Acl\Acl $acl*/
            $acl = ObjectManager::make($attribute->getName(),$attribute->getArguments());
            $acl->setModule($data->getData('module'))
                ->setRoute($data->getData('router'))
                ->setRouter($data->getData('base_router'))
                ->setClass($data->getData('class'))
                ->setMethod($data->getData('request_method'))
                ->setType($type)
            ;
            $this->acl->beginTransaction();
            try {
                $this->acl->insert($acl->getData(),$this->acl->getModelFields())->fetch();
                $this->acl->commit();
            }catch (\Exception $exception){
                $this->acl->rollBack();
                dd($exception);
            }
        }

    }
}