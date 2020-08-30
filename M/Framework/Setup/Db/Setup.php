<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/28
 * 时间：15:03
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Setup\Db;


use M\Framework\App\Exception;
use M\Framework\Database\Db\Ddl\Table;
use M\Framework\Database\DbManager;
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
    function createTable(string $table_name, string $comment = '')
    {
        $table_name = $this->getTable($table_name);
        return new Table($table_name, $comment);
    }

    /**
     * @DESC         |获取前缀
     *
     * 参数区：
     *
     */
    function getTablePrefix(): string
    {
        $type = $this->getConfig('default');
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
    function tableExist(string $table): bool
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
    function getTable(string $name = ''): string
    {
        return $this->getTablePrefix() . $name;
    }

    /**
     * @DESC         |删除表
     *
     * 参数区：
     *
     * @param string $tableName
     * @return bool
     */
    function dropTable(string $tableName)
    {
        if (!strstr($tableName, $this->getTablePrefix())) $tableName = $this->getTable($tableName);
        try {
            $this->query('drop table ' . $tableName);
            return true;
        } catch (Exception $exception) {
            return false;
        }
    }
}