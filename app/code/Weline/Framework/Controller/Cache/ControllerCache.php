<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Controller\Cache;

class ControllerCache extends \Weline\Framework\Cache\CacheManager
{
    public function __construct(string $identity = 'framework_controller')
    {
        parent::__construct($identity);
    }
}
