<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/3/6 23:01:21
 */

namespace Weline\Eav\Model\Attribute;

use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Value extends \Weline\Framework\Database\Model
{
    public const fields_ID        = 'value_id';
    public const fields_entity    = 'entity';
    public const fields_attribute = 'attribute';
    public const fields_value     = 'value';

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
        // FIXME 读值或者设置值时需要自动创建表
//        $setup->dropTable();
//        if(!$setup->tableExist()){
//            $setup->createTable()
//        }
    }
}