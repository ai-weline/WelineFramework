<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Setup\Db;

use Weline\Framework\App\Exception;
use Weline\Framework\Database\Db\Ddl\Table;
use Weline\Framework\Database\DbManager;
use think\db\exception\PDOException;

class Setup extends DbManager
{
    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $table_name
     * @param string $comment
     * @return Table
     */
    public function createTable(string $table_name, string $comment = ''): Table
    {
        $table_name = $this->getTable($table_name);

        return new Table($table_name, $comment);
    }

    /**
     * @DESC         |获取前缀
     *
     * 参数区：
     */
    public function getTablePrefix(): string
    {
        $type   = $this->getConfig('default');
        $prefix = $this->getConfig('connections')[$type]['prefix'];

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
        if (! strstr($name, $this->getTablePrefix())) {
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
    public function dropTable(string $tableName)
    {
        if (! strstr($tableName, $this->getTablePrefix())) {
            $tableName = $this->getTable($tableName);
        }

        try {
            $this->query('drop table ' . $tableName);

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}
