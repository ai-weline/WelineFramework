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

class EventsManager
{
//    protected  WeakMaps $observers;# php8
    /**@var $events Event[] */
    protected array $events = [];

    protected array $eventsObservers=[];

    /**
     * @var Reader
     */
    private Reader $reader;

    public function __construct(
        Reader $reader
    ) {
        $this->reader = $reader;
    }

    public function scanEvents()
    {
        if (empty($this->eventsObservers)) {
            foreach ($this->reader->read() as $module_and_file => $eventObservers) {
                $this->eventsObservers = array_merge($this->eventsObservers, $eventObservers);
            }
        }

        return $this->eventsObservers;
    }

    public function getEventObservers(string $eventName)
    {
        $evenObserverLists = $this->scanEvents();

        return $evenObserverLists[$eventName] ?? [];
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
        if (! isset($this->events[$eventName])) {
            $data['observers'] = $this->getEventObservers($eventName);
            $this->events      = array_merge($this->events, [$eventName=>(new Event($data))->setName($eventName)]);
//            $this->events[$eventName] = (new Event($data))->setName($eventName);
        }
//        p($this->events, 1);
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
