<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Event;

use Weline\Framework\Event\Config\Reader;
use Weline\Framework\App\Exception;
use Weline\Framework\Xml\Parser;

class EventsManager
{
//    protected  WeakMaps $observers;# php8
    /**@var $events Event[] */
    protected array $events;

    /**
     * @var Parser
     */
    private Parser $parser;

    /**
     * @var Reader
     */
    private Reader $reader;

    public function __construct(
        Parser $parser,
        Reader $reader
    ) {
        $this->parser = $parser;
        $this->reader = $reader;
    }

    public function scanEvents()
    {
        return $this->reader->read();
    }

    public function getEventObservers(string $eventName)
    {
        $evenObserverLists = $this->scanEvents();
        foreach ($evenObserverLists as $module_and_file=>$evenObserver) {
            return $evenObserver[$eventName] ?? [];
        }

        return [];
    }

    /**
     * @DESC         |添加事件
     *
     * 参数区：
     *
     * @param string $eventName
     * @param array $data
     * @throws \Weline\Framework\Exception\Core
     * @return $this
     */
    public function dispatch(string $eventName, array $data)
    {
        $data['observers']        =$this->getEventObservers($eventName);
        $this->events[$eventName] = (new Event($data))->setName($eventName);
        $this->events[$eventName]->dispatch();

        return $this;
    }

    /**
     * @DESC         |添加观察者
     *
     * 参数区：
     *
     * @param string $eventName
     * @param Observer $observer
     * @throws Exception
     * @return $this
     */
    public function addObserver(string $eventName, Observer $observer)
    {
        if (! isset($this->events[$eventName])) {
            throw new Exception(__(sprintf('事件异常：%1 事件不存在！', $eventName)));
        }
        $event = $this->events[$eventName];
        $event->addObserver($observer);

        return $this;
    }

    /**
     * @DESC         |触发运行器
     *
     * 参数区：
     * @param string $eventName
     * @throws Exception
     */
    public function trigger(string $eventName)
    {
        if (! isset($this->events[$eventName])) {
            throw new Exception(__(sprintf('事件异常：%1 事件不存在！', $eventName)));
        }
        $event = $this->events[$eventName];
        $event->dispatch();
    }
}
