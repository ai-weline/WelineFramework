<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/1/4 23:28:20
 */

namespace Weline\Framework\Acl;

use Weline\Framework\Console\Cli;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;

#[\Attribute] class Acl extends DataObject
{
    /**
     * 给路由上权限控制
     *
     * @param string $source_name
     * @param string $document
     * @param string $parent_source
     * @param string $rewrite
     *
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     */
    public function __construct(string $source_name, string $document, string $parent_source, string $rewrite = '')
    {
        parent::__construct([
                                'source_name'   => $source_name,
                                'document'      => $document,
                                'parent_source' => $parent_source,
                                'rewrite'       => $rewrite,
                            ]);
        if (!CLI) {
            dd($this->getData());
            /**@var EventsManager $eventsManager */
            $eventsManager = ObjectManager::getInstance(EventsManager::class);
            $eventsManager->dispatch('Weline_Acl::control', ['data' => $this]);
        }

    }

    function setModule(string $module_name): Acl
    {
        return $this->setData('module', $module_name);
    }

    function setRouter(string $router): Acl
    {
        return $this->setData('router', $router);
    }

    function setRoute(string $route): Acl
    {
        return $this->setData('route', $route);
    }

    function setMethod(string $method = ''): Acl
    {
        return $this->setData('method', $method);
    }

    function setDocument(string $document = ''): Acl
    {
        return $this->setData('document', $document);
    }

    function setClass(string $class): Acl
    {
        return $this->setData('class', $class);
    }

    function setType(string $type = ''): Acl
    {
        return $this->setData('type', $type);
    }

    function getModule(): string
    {
        return $this->getData('module');
    }

    function getMethod(): string
    {
        return $this->getData('method');
    }

    function getRouter(): string
    {
        return $this->getData('router');
    }

    function getRoute(): string
    {
        return $this->getData('route');
    }

    function getSourceName(): string
    {
        return $this->getData('source_name');
    }

    function getDocument(): string
    {
        return $this->getData('document');
    }

    function getClass(): string
    {
        return $this->getData('class');
    }

    function getType(): string
    {
        return $this->getData('type');
    }

    function getParentSource(): string
    {
        return $this->getData('parent_source');
    }

    function execute(): void
    {
        // ACL控制器事件分配
        /**@var EventsManager $eventsManager */
        $eventsManager = ObjectManager::getInstance(EventsManager::class);
        $eventsManager->dispatch('Framework_Acl::dispatch', ['data' => $this]);
    }
}