<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/1/20
 * 时间：23:48
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
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


    function __construct(
        EventCache $eventCache,
        Scanner $scanner,
        Parser $parser,
        $path = 'event.xml'
    )
    {
        parent::__construct($scanner, $parser, $path);
        $this->parser = $parser;
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
    function read($path = 'config/event', $cache = true)
    {
        if ($cache && $event = $this->eventCache->get('event')) {
            return $event;
        }
        $event = parent::read($path);
        $this->eventCache->add('event', $event);
        return $event;
    }
}