<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/22 19:38:43
 */

namespace Weline\Eav\Model\EavAttribute;

use Weline\Framework\Database\Api\Db\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Group extends \Weline\Framework\Database\Model
{
    public const fields_ID          = 'code';
    public const fields_name        = 'name';
    public const fields_code        = 'code';
    public const fields_set_code    = 'set_code';
    public const fields_entity_code = 'entity_code';

    public array $_unit_primary_keys = ['entity_code'];

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
            $setup->createTable('属性组')
                  ->addColumn(self::fields_ID, TableInterface::column_type_VARCHAR, 255, 'not null', '属性组代码')
                  ->addColumn(self::fields_set_code, TableInterface::column_type_VARCHAR, 255, 'not null', '属性集代码')
                  ->addColumn(self::fields_name, TableInterface::column_type_VARCHAR, 255, 'not null', '属性组名')
                  ->addColumn(self::fields_entity_code, TableInterface::column_type_VARCHAR, 255, 'not null', '实体')
                  ->addConstraints('PRIMARY KEY (`' . self::fields_ID . '`,`' . self::fields_entity_code . '`)')
                  ->addIndex(
                      TableInterface::index_type_KEY,
                      'EAV_GROUP_KEY',
                      [self::fields_code, self::fields_entity_code],
                      '组KEY索引',
                      ''
                  )
                  ->create();
        }
    }
}