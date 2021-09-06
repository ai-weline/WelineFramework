<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Model;

use Weline\Framework\Database\Model;
use Weline\Framework\Setup\Db\ModelSetup;

class AiwelineHelloWorld extends Model
{

    function provideTable(): string
    {
        return '';
    }

    function providePrimaryField(): string
    {
        return '';
    }

    function setup(ModelSetup $setup): void
    {
        // TODO: Implement setup() method.
    }
}
