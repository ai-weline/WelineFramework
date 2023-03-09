<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/6 20:25:54
 */

namespace Weline\Eav\Model;

use Weline\Framework\App\Exception;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Attribute extends \Weline\Framework\Database\Model
{

    public const fields_ID                   = 'code';
    public const fields_code                 = 'code';
    public const fields_name                 = 'name';
    public const fields_type                 = 'type';
    public const fields_entity               = 'entity';
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
        $setup->dropTable();
        if (!$setup->tableExist()) {
            $setup->createTable('属性表')
                  ->addColumn(
                      self::fields_ID,
                      TableInterface::column_type_INTEGER,
                      0,
                      'primary key auto_increment',
                      'ID')
                  ->addColumn(
                      self::fields_code,
                      TableInterface::column_type_VARCHAR,
                      60,
                      'unique',
                      '代码')
                  ->addColumn(
                      self::fields_entity,
                      TableInterface::column_type_VARCHAR,
                      60,
                      'not null',
                      '所属实体')
                  ->addColumn(
                      self::fields_name,
                      TableInterface::column_type_VARCHAR,
                      120,
                      'not null',
                      '名称')
                  ->addColumn(
                      self::fields_type,
                      TableInterface::column_type_VARCHAR,
                      120,
                      'not null',
                      '类型')
                  ->create();
        }
    }

    function getEntity(): string
    {
        return $this->getData(self::fields_entity) ?: '';
    }


    function setEntity(string $entity): static
    {
        return $this->setData(self::fields_entity, $entity);
    }

    function getCode(): string
    {
        return $this->getData(self::fields_code) ?: '';
    }

    function setCode(string $code): static
    {
        return $this->setData(self::fields_code, $code);
    }

    function getType(): string
    {
        return $this->getData(self::fields_type) ?: '';
    }

    function setType(string $type): static
    {
        return $this->setData(self::fields_type, $type);
    }


    function getName(): string
    {
        return $this->getData(self::fields_name) ?: '';
    }

    function setName(string $name): static
    {
        return $this->setData(self::fields_name, $name);
    }

    function getValue()
    {
        if (!$this->getData(self::fields_entity)) {
            throw new Exception(__('该模型没有实体！'));
        }

    }
}