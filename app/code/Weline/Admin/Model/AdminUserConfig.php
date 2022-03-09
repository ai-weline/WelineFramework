<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Model;

use Weline\Admin\Session\AdminSession;
use Weline\Backend\Model\Config;
use Weline\Framework\Database\Api\Db\TableInterface;
use Weline\Framework\Database\Db\Ddl\Table;
use Weline\Framework\Manager\ObjectManager;
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
//        /**@var Config $config*/
//        $config = ObjectManager::getInstance(Config::class);
//        $config->setConfig('admin_default_avatar', 'Weline_Admin::/img/logo.png', 'Weline_Admin');
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

    function addConfig(string|array $key, mixed $data = null): static
    {
        try {
            $config = $this->getOriginConfig() ? json_decode($this->getOriginConfig(), true) : [];
        } catch (\Exception $exception) {
            $config = [];
        }
        if (is_array($key)) {
            $config = array_merge($config, $key);
        } else {
            $config[$key] = $data;
        }
        $this->setData(self::fields_config, json_encode($config));
        return $this;
    }

    function getOriginConfig()
    {
        return $this->getData(self::fields_config);
    }

    function getConfig(string $key)
    {
        try {
            $config = $this->getOriginConfig() ? json_decode($this->getOriginConfig(), true) : [];
            return $config[$key] ?? '';
        } catch (\Exception $exception) {
            return '';
        }
    }

    function save(bool|array $data = [], string $sequence = null): bool
    {
        $this->forceCheck();
        return parent::save($data, $sequence);
    }
}