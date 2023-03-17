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
use Weline\Eav\Model\Test;
use Weline\Framework\App\Exception;
use Weline\Framework\Manager\ObjectManager;
use function PHPUnit\Framework\assertTrue;

class EavTest extends \Weline\Framework\UnitTest\TestCore
{
    private Test $test;
    private Attribute $attribute;
    const multi_attr  = 'test_multi';
    const single_attr = 'test_single';

    public function setUp(): void
    {
        parent::setUp();
        $this->test      = ObjectManager::getInstance(Test::class)->load(1);
        $this->attribute = ObjectManager::getInstance(Attribute::class);
        $this->value     = ObjectManager::getInstance(Attribute\Type\Value::class);
    }

    function testAddAttribute()
    {
        $this->test->unsetAttribute(self::single_attr);
        $assertion = $this->test->addAttribute(self::single_attr, '测试', 'input_int');
        self::assertTrue($assertion, 'Eav添加属性测试');
    }

    function testAddMultiAttribute()
    {
        $this->test->unsetAttribute(self::multi_attr);
        $assertion = $this->test->addAttribute(self::multi_attr, '测试(多值属性)', 'input_int', true);
        self::assertTrue($assertion, 'Eav添加属性测试');
    }

    function testGetAttribute()
    {
        $this->testAddAttribute();
        $result = $this->test->getAttribute(self::single_attr);
        self::assertTrue($result->getId() === self::single_attr, '获取属性');
    }

    function testGetAttributes()
    {
        $this->testAddAttribute();
        $this->testAddMultiAttribute();
        $result = $this->test->getAttributes();
        self::assertIsArray($result, '获取所有属性');
    }

    function testSetAttribute()
    {
        $attribute = $this->test->getAttribute(self::single_attr);
        $attribute->setName('测试修改属性名1');
        $assertion1 = $this->test->setAttribute($attribute);
        $attribute  = $this->test->getAttribute(self::multi_attr);
        $attribute->setName('测试修改属性名(多值)');
        $assertion2 = $this->test->setAttribute($attribute);
        self::assertTrue($assertion1 && $assertion2, 'Eav设置属性测试');
    }

    function testSetSingleValueAttributeValue()
    {
        $attribute = $this->test->getAttribute(self::single_attr);
        try {
            $attribute->setValue(1, 2);
            assertTrue(true, '设置单值属性值');
        } catch (Exception $e) {
            assertTrue(false, '设置单值属性值：' . $e->getMessage());
        }

    }

    function testSetMultiValueAttributeValue()
    {
        $attribute = $this->test->getAttribute(self::multi_attr);
        try {
            $attribute->setValue(1, [1, 3, 5]);
            assertTrue(true, '设置单值属性值');
        } catch (Exception $e) {
            assertTrue(false, '设置单值属性值：' . $e->getMessage());
        }

    }

    function testGetSingleValueAttributeValueByEntity()
    {
        $this->testAddAttribute();
        $this->testSetSingleValueAttributeValue();
        $result = $this->test->getAttribute(self::single_attr, 1);
        self::assertTrue($result->getData(Attribute::value_key) === 2, '获取实体属性');
    }
    function testGetMultiValueAttributeValueByEntity()
    {
        $this->testAddMultiAttribute();
        $this->testSetMultiValueAttributeValue();
        $result = $this->test->getAttribute(self::multi_attr, 1);
        self::assertTrue($result->getData(Attribute::value_key) === [1, 3, 5], '获取实体属性');
    }

    function testUnsetAttribute(){
        $this->testAddAttribute();
        $this->testAddMultiAttribute();
        $this->testSetSingleValueAttributeValue();
        $this->testSetMultiValueAttributeValue();
        $s1 = $this->test->unsetAttribute(self::single_attr);
        $s2 = $this->test->unsetAttribute(self::multi_attr);
        $a1 = $this->test->getAttribute(self::single_attr);
        $a2 = $this->test->getAttribute(self::multi_attr);
        if($a1->getValue()===2){
            $s1 = true;
        }
        if($a2->getValue()===[1, 3, 5]){
            $s1 = true;
        }
        $this->testAddAttribute();
        $this->testAddMultiAttribute();
        $this->testSetSingleValueAttributeValue();
        $this->testSetMultiValueAttributeValue();
        $this->test->unsetAttribute(self::single_attr,true);
        $this->test->unsetAttribute(self::multi_attr,true);

        $a1 = $this->test->getAttribute(self::single_attr);
        $a2 = $this->test->getAttribute(self::multi_attr);
        if($a1->getValue()===2){
            $s1 = false;
        }
        if($a2->getValue()===[1, 3, 5]){
            $s1 = false;
        }
        assertTrue($s1&&$s2,'删除属性成功');
    }

    //FIXME 完成属性值匹配读写
}