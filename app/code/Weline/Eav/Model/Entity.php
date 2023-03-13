<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/6 20:24:56
 */

namespace Weline\Eav\Model;

use Weline\Framework\Database\Api\Db\TableInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Entity extends \Weline\Framework\Database\Model
{
    public const fields_ID                     = 'code';
    public const fields_code                   = 'code';
    public const fields_name                   = 'name';
    public const fields_class                  = 'class';
    public const fields_entity_id_field_type   = 'entity_id_field_type';
    public const fields_entity_id_field_length = 'entity_id_field_length';

    /**
     * @inheritDoc
     */
    public function setup(ModelSetup $setup, Context $context): void
    {
        $this->install($setup, $context);
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    public function install(ModelSetup $setup, Context $context): void
    {
//        $setup->dropTable();
        if (!$setup->tableExist()) {
            $setup->createTable('Eav实体表')
                  ->addColumn(
                      self::fields_ID,
                      TableInterface::column_type_VARCHAR,
                      60,
                      'primary key',
                      '实体代码')
                  ->addColumn(
                      self::fields_name,
                      TableInterface::column_type_VARCHAR,
                      60,
                      'not null',
                      '实体名')
                  ->addColumn(
                      self::fields_class,
                      TableInterface::column_type_VARCHAR,
                      255,
                      'not null',
                      '实体类')
                  ->addColumn(
                      self::fields_entity_id_field_type,
                      TableInterface::column_type_VARCHAR,
                      60,
                      'not null',
                      '实体ID字段类型')
                  ->addColumn(
                      self::fields_entity_id_field_length,
                      TableInterface::column_type_SMALLINT,
                      5,
                      'not null',
                      '实体ID字段长度')
                  ->create();
        }
    }

    public function getAttribute(string $code)
    {
        /**@var \Weline\Eav\Model\Attribute $attributeModel */
        $attributeModel = ObjectManager::getInstance(Attribute::class);
        $attributeModel->where(Attribute::fields_entity, $this->getCode())
                       ->where(Attribute::fields_code, $code)
                       ->find()
                       ->fetch();
        return $attributeModel;
    }

    public function getCode(): string
    {
        return $this->getData(self::fields_code);
    }

    public function setCode(string $code): static
    {
        return $this->setData(self::fields_code, $code);
    }

    public function getName(): string
    {
        return $this->getData(self::fields_name);
    }

    public function setName(string $name): static
    {
        return $this->setData(self::fields_name, $name);
    }

    public function getClass(): string
    {
        return $this->getData(self::fields_class);
    }

    public function setClass(string $class): static
    {
        return $this->setData(self::fields_class, $class);
    }

    public function getEntityIdFieldType(): string
    {
        return $this->getData(self::fields_entity_id_field_type);
    }

    public function setEntityIdFieldType(string $entity_id_field_type): static
    {
        return $this->setData(self::fields_entity_id_field_type, $entity_id_field_type);
    }

    public function getEntityIdFieldLength(): int
    {
        return intval($this->getData(self::fields_entity_id_field_length));
    }

    public function setEntityIdFieldLength(int $entity_id_field_length): static
    {
        return $this->setData(self::fields_entity_id_field_length, $entity_id_field_length);
    }
}