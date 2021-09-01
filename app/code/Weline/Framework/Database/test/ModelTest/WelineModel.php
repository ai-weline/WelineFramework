<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\test\ModelTest;


class WelineModel extends \Weline\Framework\Database\Model
{

    function provideTable(): string
    {
        return 'weline';
    }

    function providePrimaryField(): string
    {
        return '';
    }

    function provideFields(): array
    {
        return [];
    }
}