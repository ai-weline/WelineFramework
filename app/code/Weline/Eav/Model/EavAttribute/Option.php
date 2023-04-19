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
    public const fields_ID             = 'code';
    public const fields_CODE           = 'code';
    public const fields_attribute_code = 'attribute_code';
    public const fields_entity_code    = 'entity_code';
    public const fields_name           = 'name';

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
            $setup->createTable('属性配置项')
                  ->addColumn(self::fields_ID, TableInterface::column_type_VARCHAR, 255, 'not null', '配置项代码')
                  ->addColumn(self::fields_attribute_code, TableInterface::column_type_VARCHAR, 255, 'not null', '相关属性')
                  ->addColumn(self::fields_entity_code, TableInterface::column_type_VARCHAR, 255, 'not null', '相关实体')
                  ->addColumn(self::fields_name, TableInterface::column_type_VARCHAR, 255, 'not null', '配置名')
                  ->addConstraints('primary key (' . self::fields_ID . ',' . self::fields_attribute_code . ',' . self::fields_entity_code . ')')
                  ->create();
        }
    }
}