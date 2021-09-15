<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\SystemConfig\Model;

use Weline\Backend\Cache\BackendCache;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Exception\Core;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class SystemConfig extends \Weline\Framework\Database\Model
{
    const field_KEY = 'key';
    const field_VALUE = 'v';
    const field_MODULE = 'module';
    const field_AREA = 'area';

    const area_BACKEND = 'backend';
    const area_FRONTEND = 'frontend';

    private CacheInterface $cache;
    private array $cache_data;

    function __construct(
        BackendCache $cache,
        array        $data = []
    )
    {
        $this->cache = $cache->create();
        parent::__construct($data);
    }

    function providePrimaryField(): string
    {
        return self::field_KEY;
    }

    /**
     * @DESC          # 获取配置
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/14 21:25
     * 参数区：
     * @param string $key
     * @param string $area
     * @param string $module
     * @return mixed
     */
    function getConfig(string $key, string $module, string $area): mixed
    {
        $cache_key = 'system_config_cache_' . $key . '_' . $area . '_' . $module;
        if (!DEV && $cache_data = $this->cache->get($cache_key)) {
            return $cache_data;
        }
        $config_value = $this->where([['key', $key], ['area', $area], ['module', $module]])->find()->fetch();
        $result = null;
        if (isset($config_value['v'])) {
            $result = $config_value['v'];
        }
        if (!DEV) {
            $this->cache->set($cache_key, $result);
        }
        return $result;
    }

    /**
     * @DESC          # 设置配置
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/14 21:25
     * 参数区：
     * @param string $key
     * @param string $value
     * @param string $area
     * @param string $module
     * @return bool
     */
    function setConfig(string $key, string $value, string $module, string $area): bool
    {
        $cache_key = 'system_config_cache_' . $key . '_' . $area . '_' . $module;
        try {
            $this->setData(['key' => $key, 'area' => $area, 'module' => $module, 'v' => $value])
                ->forceCheck()
                ->save();
            # 设置配置缓存
            $this->cache->set($cache_key, $value,);
            return true;
        } catch (\ReflectionException | Core $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    function setup(ModelSetup $setup, Context $context): void
    {
        /* $setup->dropTable();
         $setup->createTable('系统配置表')
             ->addColumn(self::field_KEY, TableInterface::column_type_VARCHAR, 120, 'primary key', '键')
             ->addColumn(self::field_VALUE, TableInterface::column_type_TEXT, 0, '', '值')
             ->addColumn(self::field_MODULE, TableInterface::column_type_VARCHAR, 120, 'not null', '模块')
             ->addColumn(self::field_AREA, TableInterface::column_type_VARCHAR, 120, "NOT NULL DEFAULT 'frontend'", '区域：backend/frontend')
             ->create();*/
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
        $setup->createTable('系统配置表')
            ->addColumn(self::field_KEY, TableInterface::column_type_VARCHAR, 120, 'primary key', '键')
            ->addColumn(self::field_VALUE, TableInterface::column_type_TEXT, 0, '', '值')
            ->addColumn(self::field_MODULE, TableInterface::column_type_VARCHAR, 120, 'not null', '模块')
            ->addColumn(self::field_AREA, TableInterface::column_type_VARCHAR, 120, "NOT NULL DEFAULT 'frontend'", '区域：backend/frontend')
            ->create();
    }
}