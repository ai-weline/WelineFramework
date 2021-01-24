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
        $app_events = $this->reader->read();
        // 相同
    }

    /**
     * @DESC         |添加事件
     *
     * 参数区：
     *
     * @param string $eventName
     * @param array $data
     * @return $this
     */
    public function dispatch(string $eventName, array $data)
    {
        $this->events[$eventName] = (new Event($data))->setName($eventName);

        return $this;
    }

    /**
     * @DESC         |添加观察者
     *
     * 参数区：
     *
     * @param string $eventName
     * @param ObserverInterface $observer
     * @throws Exception
     * @return $this
     */
    public function addObserver(string $eventName, ObserverInterface $observer)
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
