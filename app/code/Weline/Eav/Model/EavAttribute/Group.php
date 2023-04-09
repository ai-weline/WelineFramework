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
    public const fields_ID       = 'group_id';
    public const fields_group_id = 'group_id';
    public const fields_set_id   = 'set_id';
    public const fields_name     = 'name';
    public const fields_code     = 'code';
    public const fields_entity   = 'entity';

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
                  ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'primary key auto_increment', '属性组ID')
                  ->addColumn(self::fields_set_id, TableInterface::column_type_INTEGER, 0, 'not null', '属性集ID')
                  ->addColumn(self::fields_name, TableInterface::column_type_VARCHAR, 225, 'not null unique', '属性组名')
                  ->addColumn(self::fields_code, TableInterface::column_type_VARCHAR, 225, 'not null unique', '属性组代码')
                  ->addColumn(self::fields_entity, TableInterface::column_type_VARCHAR, 225, 'not null', '实体')
                  ->create();
        }
    }
}