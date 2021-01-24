<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Event\Config;

use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Event\Cache\EventCache;
use Weline\Framework\System\File\Scanner;
use Weline\Framework\Xml\Parser;

class Reader extends \Weline\Framework\Config\Reader
{
    /**
     * @var Parser
     */
    private Parser $parser;

    /**
     * @var CacheInterface
     */
    private CacheInterface $eventCache;

    public function __construct(
        EventCache $eventCache,
        Scanner $scanner,
        Parser $parser,
        $path = 'event.xml'
    ) {
        parent::__construct($scanner, $parser, $path);
        $this->parser     = $parser;
        $this->eventCache = $eventCache->create();
    }

    /**
     * @DESC         |读取事件配置
     *
     * 参数区：
     *
     * @param string $path
     * @param bool $cache
     * @return mixed
     */
    public function read($path = 'config/event', $cache = true)
    {
        if ($cache && $event = $this->eventCache->get('event')) {
            return $event;
        }
        $event = parent::read($path);
        $this->eventCache->add('event', $event);

        return $event;
    }
}
