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

use Weline\Eav\Model\Attribute;
use Weline\Eav\Model\Eav\Test;
use Weline\Eav\Model\Entity;
use Weline\Eav\test\Eav\Product;
use Weline\Framework\App\Exception;
use Weline\Framework\Manager\ObjectManager;
use function PHPUnit\Framework\assertTrue;

class EavTest extends \Weline\Framework\UnitTest\TestCore
{
    private Test $test;
    private Attribute $attribute;

    public function setUp(): void
    {
        parent::setUp();
        $this->test      = ObjectManager::getInstance(Test::class);
        $this->attribute = ObjectManager::getInstance(Attribute::class);
        $this->value     = ObjectManager::getInstance(Attribute\Type\Value::class);
    }

    function testAddAttribute()
    {
        $this->test->unsetAttribute('test');
        $assertion = $this->test->addAttribute('test', '测试', 'input_int');
        self::assertTrue($assertion, 'Eav添加属性测试');
    }

    function testAddMultiValueAttribute()
    {
        $this->test->unsetAttribute('test_multi');
        $assertion = $this->test->addAttribute('test_multi', '测试(多值属性)', 'input_int', true);
        self::assertTrue($assertion, 'Eav添加属性测试(多值属性)');
    }

    function testGetAttribute()
    {
        $this->testAddAttribute();
        $result = $this->test->getAttribute('test');
        self::assertTrue($result->getId() === 'test', '获取属性');
    }

    function testSetAttribute()
    {
        $attribute = $this->test->getAttribute('test');
        $attribute->setName('测试修改属性名1');
        $assertion1 = $this->test->setAttribute($attribute);
        $attribute  = $this->test->getAttribute('test_multi');
        $attribute->setName('测试修改属性名(多值)');
        $assertion2 = $this->test->setAttribute($attribute);
        self::assertTrue($assertion1 && $assertion2, 'Eav设置属性测试');
    }

    function testSetSingleValueAttributeValue()
    {
        $attribute = $this->test->getAttribute('test');
        try {
            $attribute->setValue(1, 1);
            assertTrue(true, '设置单值属性值');
        } catch (Exception $e) {
            assertTrue(false, '设置单值属性值：' . $e->getMessage());
        }

    }

    function testSetMultiValueAttributeValue()
    {
        // FIXME 多值属性
        $attribute = $this->test->getAttribute('test');
        try {
            $attribute->setValue(1, 1);
            assertTrue(true, '设置单值属性值');
        } catch (Exception $e) {
            assertTrue(false, '设置单值属性值：' . $e->getMessage());
        }

    }

    function testGetAttributeByEntity()
    {
        $this->testAddAttribute();
        $result = $this->test->getAttribute('test', 1);
        self::assertTrue(($result->getId() === 'test') && ($result->getValue()), '获取实体属性');
    }

    function testGetAttributes()
    {
        $result = $this->test->getAttributes();
        self::assertIsArray($result, 'Eav获取所有属性');
        $result = $this->test->getAttributes(1, 1);
        self::assertIsArray($result, 'Eav获取实例所有属性');
    }
}