<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin;

/**
 * 文件信息
 * DESC:   |
 * 作者：   秋枫雁飞
 * 日期：   2021/2/2
 * 时间：   21:34
 * 网站：   https://bbs.aiweline.com
 * Email：  aiweline@qq.com
 * @DESC:    此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @since 1.2
 *
 * Class AbstractInterceptor 所有Interceptor的基类
 * @package Weline\Framework\Plugin
 */
abstract class AbstractInterceptor implements Api\InterceptorInterface
{
    final public function invoke($object, $method, $args = null)
    {
        $this->doBefore();
        if (method_exists($object, $method)) {
            $object->$method($args);
        }
        $this->doAfter();
    }
}
