<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/1/24
 * 时间：15:10
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Event\Cache;


/**
 * 文件信息
 * DESC:   |
 * 作者：   秋枫雁飞
 * 日期：   2021/1/24
 * 时间：   15:16
 * 网站：   https://bbs.aiweline.com
 * Email：  aiweline@qq.com
 * @DESC:    此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @since 1.2
 *
 * Class EventCache
 * @package M\Framework\Event\Cache
 * @since 100
 */
class EventCache extends \Weline\Framework\Cache\CacheManager
{
    function __construct(string $identity = 'event')
    {
        parent::__construct($identity);
    }
}