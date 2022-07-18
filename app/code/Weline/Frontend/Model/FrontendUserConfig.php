<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Frontend\Model;

use Weline\Framework\Database\AbstractModel;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class FrontendUserConfig extends \Weline\Framework\Database\Model
{
    public const fields_ID     = 'user_id';
    public const fields_config = 'config';

    /**
     * @inheritDoc
     */
    public function setup(ModelSetup $setup, Context $context): void
    {
//        $setup->dropTable();
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
        if (!$setup->tableExist()) {
            $setup->createTable()
                  ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, null, 'primary key', '用户ID')
                  ->addColumn(self::fields_config, TableInterface::column_type_TEXT, null, '', '配置')
                  ->create();
        }
    }

    public function setUserId(int $admin_user_id)
    {
        return $this->setData(self::fields_ID, $admin_user_id);
    }

    public function addConfig(string|array $key, mixed $data = null): static
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

    public function getOriginConfig()
    {
        return $this->getData(self::fields_config);
    }

    public function getConfig(string $key)
    {
        try {
            $config = $this->getOriginConfig() ? json_decode($this->getOriginConfig(), true) : [];
            return $config[$key] ?? '';
        } catch (\Exception $exception) {
            return '';
        }
    }

    public function save(array|bool|AbstractModel $data = [], string $sequence = null): bool
    {
        $this->forceCheck();
        return parent::save($data, $sequence);
    }
}
