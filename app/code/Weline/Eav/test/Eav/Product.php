<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/6 22:40:28
 */

namespace Weline\Eav\test\Eav;

use Weline\Eav\AbstractEav;

class Product extends AbstractEav
{
    const entity_code = 'test';
    const entity_name = '测试实体';
}