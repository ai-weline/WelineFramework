<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Event\Console\Event;

use Weline\Framework\Event\Config\Reader;
use Weline\Framework\Output\Cli\Printing;

class Data implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @var Printing
     */
    private Printing $printing;

    /**
     * @var Reader
     */
    private Reader $reader;

    public function __construct(
        Reader $reader,
        Printing $printing
    ) {
        $this->printing = $printing;
        $this->reader   = $reader;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        $eventList = $this->reader->read();
        foreach ($eventList as $key => $item) {
            $this->printing->setup(__('事件监听文件：') . $key);
            foreach ($item as $k => $i) {
                $this->printing->printing(__('事件名：') . $k);
                $this->printing->printList($i);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return __('事件观察者列表！');
    }
}
