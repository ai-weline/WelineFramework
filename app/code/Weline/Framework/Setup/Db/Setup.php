<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Setup\Db;

use PDOException;
use Weline\Framework\App\Exception;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Database\Db\Ddl\Table;
use Weline\Framework\Database\Db\DdlFactory;
use Weline\Framework\Database\DbManager;
use Weline\Framework\Database\DbManager\ConfigProvider;

class Setup extends DbManager
{
    private Table $ddl_table;

    /**
     * Setup constructor.
     * @param ConfigProvider $configProvider
     * @param DdlFactory $ddl_table
     * @throws Exception
     * @throws \ReflectionException
     */
    function __construct(
        ConfigProvider $configProvider,
        DdlFactory $ddl_table
    )
    {
        parent::__construct($configProvider);
        $this->ddl_table = $ddl_table->create();
    }

    /**
     * @DESC         | 创建表
     *
     * 参数区：
     *
     * @param string $table_name
     * @param string $comment
     * @return Table\Create
     */
    public function createTable(string $table_name, string $comment = ''): Table\Create
    {
        $table_name = $this->getTable($table_name);
        return $this->ddl_table->createTable()->createTable($table_name, $comment);
    }

    /**
     * @DESC         |修改表
     *
     * 参数区：
     *
     * @param string $table_name
     * @param string $comment
     * @return Table\Alter
     */
    public function alterTable(string $table_name, string $comment = ''): Table\Alter
    {
        $table_name = $this->getTable($table_name);
        return $this->ddl_table->alterTable()->forTable($table_name, $comment);
    }

    /**
     * @DESC          # 获取前缀
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:27
     * 参数区：
     * @return string
     */
    public function getTablePrefix(): string
    {
        $prefix = $this->getConfig()->getPrefix();
        return $prefix ?? '';
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     * @param string $table
     * @return bool
     */
    public function tableExist(string $table): bool
    {
        $table = $this->getTable($table);

        try {
            $this->query("desc {$table}");
            return true;
        } catch (PDOException $exception) {
            return false;
        }
    }

    /**
     * @DESC         |获取表名
     *
     * 参数区：
     *
     * @param string $name
     * @return string
     */
    public function getTable(string $name = ''): string
    {
        if (!strstr($name, $this->getTablePrefix())) {
            $name = $this->getTablePrefix() . $name;
        }
        return $name;
    }

    /**
     * @DESC         |删除表
     *
     * 参数区：
     *
     * @param string $tableName
     * @return bool
     */
    public function dropTable(string $tableName): bool
    {
        if (!strstr($tableName, $this->getTablePrefix())) {
            $tableName = $this->getTable($tableName);
        }
        try {
            $this->query('drop table ' . $tableName);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @DESC          # 方法描述
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:56
     * 参数区：
     * @param string $sql
     * @return mixed
     * @throws Exception
     * @throws \ReflectionException
     * @throws \Weline\Framework\Database\Exception\LinkException
     */
    function query(string $sql): mixed
    {
        return $this->getLinker()->query($sql)->fetch();
    }
}
