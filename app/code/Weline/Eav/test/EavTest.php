<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/6 22:38:34
 */

namespace Weline\Eav\test;

use Weline\Eav\test\Eav\Product;
use Weline\Framework\Manager\ObjectManager;

class EavTest extends \Weline\Framework\UnitTest\TestCore
{
    private Product $product;

    public function setUp(): void
    {
        parent::setUp();
        $this->product = ObjectManager::getInstance(Product::class);
    }

    function testAddAttribute()
    {
        $assertion = $this->product->addAttribute('name', '产品名', 'input');
        self::assertTrue($assertion === true, 'Eav添加属性测试成功！');
    }

    function testGetAttribute()
    {
//        $result = $this->product->getAttribute($entity_id, $code)
    }

    function testGetAttributes()
    {
        $result = $this->product->getAttributes();
        self::assertIsArray($result, 'Eav获取属性成功！');
    }
}