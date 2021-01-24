<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/1/24
 * 时间：15:11
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Config\Cache;


class ConfigCache extends \Weline\Framework\Cache\CacheManager
{
    function __construct(string $identity = 'config')
    {
        parent::__construct($identity);
    }
}