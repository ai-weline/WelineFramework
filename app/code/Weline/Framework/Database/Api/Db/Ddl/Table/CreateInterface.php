<?php
declare(strict_types=1);
namespace Weline\Framework\Database\Api\Db\Ddl\Table;
/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Database\Api\Connection\QueryInterface;
use Weline\Framework\Database\ConnectionFactory;

interface CreateInterface extends TableInterface {

    /**
     * @DESC          # 创建表
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 17:22
     * 参数区：
     * @param string $table
     * @param string $comment
     * @return CreateInterface
     */
    public function createTable(string $table, string $comment = ''): CreateInterface;

    /**
     * @DESC          # 创建表
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:36
     * 参数区：
     * @return bool
     */
    public function create(): QueryInterface;


    /**
     * @DESC          # 数据库链接
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 17:23
     * 参数区：
     * @return ConnectionFactory
     */
    function getConnection(): ConnectionFactory;

    /**
     * @DESC          # 添加字段
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 21:31
     * 参数区：
     * @param string $field_name 字段名
     * @param string $type 字段类型
     * @param int|null $length 长度
     * @param string $options 配置
     * @param string $comment 字段注释
     * @return CreateInterface
     */
    public function addColumn(string $field_name, string $type, ?int $length, string $options, string $comment): CreateInterface;

    /**
     * @DESC          # 添加索引
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 17:26
     * 参数区：
     * @param string $type 【索引类型】
     * @param string $name 【索引名】
     * @param array|string $column
     * @return CreateInterface
     */
    public function addIndex(string $type, string $name, array|string $column): CreateInterface;

    /**
     * @DESC          # 建表附加
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 17:27
     * 参数区：
     * @param string $additional_sql 【建表时附加的SQL】
     * @return CreateInterface
     */
    public function addAdditional(string $additional_sql = 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;'): CreateInterface;

    /**
     * @DESC          # 表约束
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 17:30
     * 参数区：
     * @param string $constraints 【表约束语句】
     * @return CreateInterface
     */
    public function addConstraints(string $constraints = ''): CreateInterface;

    /**
     * @DESC          # 读取表字段
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 17:32
     * 参数区：
     * @param string $table_name
     * @return mixed
     */
    public function getTableColumns(string $table_name = ''): mixed;


    /**
     * @DESC          # 查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 17:33
     * 参数区：
     * @param string $sql
     * @return \Weline\Framework\Database\Api\Connection\QueryInterface
     */
    public function query(string $sql): \Weline\Framework\Database\Api\Connection\QueryInterface;

    /**
     * @DESC          # 数据库类型
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:36
     * 参数区：
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getPrefix(): string;

    /**
     * @return string
     */
    public function getTable(): string;

    /**
     * @DESC          # 添加外键
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:37
     * 参数区：
     * @param string $FK_Name
     * @param string $FK_Field
     * @param string $references_table
     * @param string $references_field
     * @param false $on_delete
     * @param false $on_update
     * @return $this
     */
    function addForeignKey(string $FK_Name, string $FK_Field, string $references_table, string $references_field, bool $on_delete = false, bool $on_update = false): CreateInterface;
}