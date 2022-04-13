<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\ModuleRouter\Observer;

use Weline\Framework\Event\Event;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Router\RouterInterface;
use Weline\ModuleRouter\Config\ModuleRouterReader;

class ProcessUrlBefore implements \Weline\Framework\Event\ObserverInterface
{
    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        $data = $event->getData('data');
        $router = $event->getData('router');
        # FIXME 查找模块中的路由文件规则并提供修改后的地址给卤肉
        /**@var ModuleRouterReader $moduleRoutersReader*/
        $moduleRoutersReader = ObjectManager::getInstance(ModuleRouterReader::class);
        $moduleRouters = $moduleRoutersReader->read();
        foreach ($moduleRouters as $vendor=>$modules) {
            foreach ($modules as $module=>$moduleRouter) {
                /**@var RouterInterface $moduleRouter*/
                $moduleRouter = ObjectManager::getInstance($moduleRouter['class']);
                $moduleRouter::process($data, $router);
            }
        }
    }
}
