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

use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Attribute extends \Weline\Framework\Database\Model
{

    public const fields_ID     = 'attribute_id';
    public const fields_entity = 'entity';
    public const fields_code   = 'code';
    public const fields_name   = 'name';
    public const fields_type   = 'type';

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
}