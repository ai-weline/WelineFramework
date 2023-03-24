<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/18 13:44:09
 */

namespace Weline\Eav\Model\EavAttribute;

use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Option extends \Weline\Framework\Database\Model
{
    public const fields_ID        = 'option_id';
    public const fields_attribute = 'attribute';
    public const fields_name      = 'name';
    public const fields_entity    = 'entity';
    public const fields_option    = 'option';

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
            $setup->createTable('属性配置项')
                  ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'primary key auto_increment', '配置ID')
                  ->addColumn(self::fields_attribute, TableInterface::column_type_VARCHAR, 255, 'not null', '相关属性')
                  ->addColumn(self::fields_name, TableInterface::column_type_VARCHAR, 255, 'not null', '配置名')
                  ->addColumn(self::fields_option, TableInterface::column_type_VARCHAR, 255, 'not null', '配置项')
                  ->create();
        }
    }
}