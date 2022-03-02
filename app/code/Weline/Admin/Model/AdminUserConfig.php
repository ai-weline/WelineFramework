<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Model;

use Weline\Framework\Database\Api\Db\TableInterface;
use Weline\Framework\Database\Db\Ddl\Table;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class AdminUserConfig extends \Weline\Framework\Database\Model
{
    const fields_ID     = 'admin_user_id';
    const fields_config = 'config';

    /**
     * @inheritDoc
     */
    function setup(ModelSetup $setup, Context $context): void
    {
//        $setup->dropTable();
        if (!$setup->tableExist()) {
            $setup->createTable()
                  ->addColumn(self::fields_ID, Table::column_type_INTEGER, null, 'primary key not null', '管理员ID')
                  ->addColumn(self::fields_config, Table::column_type_TEXT, null, '', '配置JSON信息')
                  ->create();
        }
    }

    /**
     * @inheritDoc
     */
    function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    function install(ModelSetup $setup, Context $context): void
    {
        $this->setup($setup, $context);
    }

    public function setAdminUserId(int $admin_user_id)
    {
        return $this->setData(self::fields_ID, $admin_user_id);
    }

    function addConfig(string $key, mixed $data): static
    {
        try {
            $config = $this->getConfig() ? json_decode($this->getConfig(), true) : [];
        } catch (\Exception $exception) {
            $config = [];
        }
        $config[$key] = $data;
        $this->setData(self::fields_config, json_encode($config));
        return $this;
    }

    function getConfig()
    {
        return $this->getData(self::fields_config);
    }
}