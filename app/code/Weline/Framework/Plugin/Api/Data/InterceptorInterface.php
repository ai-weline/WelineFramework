<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin\Api\Data;

interface InterceptorInterface
{
    const LISTENER_BEFORE = 'before';

    const LISTENER_AROUND = 'around';

    const LISTENER_AFTER = 'after';
}
