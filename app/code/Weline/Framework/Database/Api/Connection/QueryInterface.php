<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/6/21
 * 时间：11:47
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Database\Api\Connection;


use PDOStatement;
use Weline\Framework\Database\Connection;
use Weline\Framework\Database\AbstractModel;
use Weline\Framework\Database\Connection\Query;

interface QueryInterface
{
    const attr_IDENTITY_FIELD = 'identity_field';
    const attr_TABLE = 'table';
    const attr_TABLE_ALIA = 'table_alias';
    const attr_INSERT = 'insert';
    const attr_JOIN = 'joins';
    const attr_FIELD = 'fields';
    const attr_UPDATE = 'updates';
    const attr_WHERE = 'wheres';
    const attr_BOUND_VALUE = 'bound_values';
    const attr_LIMIT = 'limit';
    const attr_ORDER = 'order';
    const attr_SQL = 'sql';
    const attr_ADDITIONAL_SQL = 'additional_sql';

    const init_vars = [
        self::attr_IDENTITY_FIELD => 'id',
        self::attr_TABLE => '',
        self::attr_TABLE_ALIA => 'main_table',
        self::attr_INSERT => array(),
        self::attr_JOIN => array(),
        self::attr_FIELD => '*',
        self::attr_UPDATE => array(),
        self::attr_WHERE => array(),
        self::attr_BOUND_VALUE => array(),
        self::attr_LIMIT => '',
        self::attr_ORDER => array(),
        self::attr_SQL => '',
        self::attr_ADDITIONAL_SQL => '',
    ];
    const query_vars = [
        self::attr_INSERT => array(),
        self::attr_JOIN => array(),
        self::attr_FIELD => '*',
        self::attr_UPDATE => array(),
        self::attr_WHERE => array(),
        self::attr_BOUND_VALUE => array(),
        self::attr_LIMIT => '',
        self::attr_ORDER => array(),
        self::attr_SQL => '',
        self::attr_ADDITIONAL_SQL => '',
    ];

    /**
     * @DESC          # 设置主键
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/25 22:02
     * 参数区：
     * @param string $field
     * @return mixed
     */
    function identity(string $field): QueryInterface;

    /**
     * @DESC          # 表名设置
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 23:01
     * 参数区：
     * @param string $table_name
     * @return QueryInterface
     */
    function table(string $table_name): QueryInterface;

    /**
     * @DESC          # 表名别名
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/17 23:25
     * 参数区：
     * @param string $table_alias_name
     * @return QueryInterface
     */
    function alias(string $table_alias_name): QueryInterface;

    /**
     * @DESC          # 更新
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 0:20
     * 参数区：
     * @param array|string $field 更新条件字段，如果查询设置有表主键则自动使用表主键
     * @param int|string $value_or_condition_field 更新数据示例：['id'=>1,'name'=>'用户']或者多值更新[['id'=>1,'name'=>'用户1'],['id'=>2,'name'=>'用户2']]
     * @return QueryInterface
     */
    function update(array|string $field, int|string $value_or_condition_field = 'id'): QueryInterface;

    /**
     * @DESC          # 表名设置
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 23:01
     * 参数区：
     * @param string $fields 示例：a.id,a.name,b.role_id,b.rule_name
     * @return QueryInterface
     */
    function fields(string $fields): QueryInterface;

    /**
     * @DESC          # 连接查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/19 21:53
     * 参数区：
     * @param string $table
     * @param string $condition
     * @param string $type
     * @return QueryInterface
     */
    function join(string $table, string $condition, string $type = 'left'): QueryInterface;

    /**
     * @DESC          | 条件查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 21:44
     * 参数区：
     * @param array|string $field 字段或者条件数组,例如：['name', 'like', '%张三%', 'AND']
     *                             第一个元素：字段名
     *                             第二个元素：逻辑符
     *                             第三个元素：条件值
     *                             第四个元素：where 查询链接符，可以不用指定，多个where时默认 AND 链接,
     *                                      多个where中使用，也可以全部where写在第一个元素中，
     *                                      就不需要多个where链接查询条件了
     * @param mixed|null $value 条件值
     * @param string $condition 逻辑符： < | = | like | > 等常规逻辑
     * @param string $where_logic 下一个where使用的逻辑 值：and | or 默认
     * @return QueryInterface
     */
    function where(array|string $field, mixed $value = null, string $condition = '=', string $where_logic = 'AND'): QueryInterface;

    /**
     * @DESC          # 限制查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 22:44
     * 参数区：
     * @param int $size
     * @param int $offset
     * @return QueryInterface
     */
    function limit(int $size, int $offset = 0): QueryInterface;

    /**
     * @DESC          # 限制查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 22:44
     * 参数区：
     * @param int $page
     * @param int $pageSize
     * @return QueryInterface
     */
    function page(int $page = 1, int $pageSize = 20): QueryInterface;

    /**
     * @DESC          # 方法描述
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 22:46
     * 参数区：
     * @param string $fields
     * @param string $sort
     * @return QueryInterface
     */
    function order(string $fields, string $sort = 'ASC'): QueryInterface;

    /**
     * @DESC          # 仅查找一个
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/17 23:15
     * 参数区：
     * @return QueryInterface
     */
    function find(): QueryInterface;

    /**
     * @DESC         |选择
     *
     * 参数区：
     *
     * @return QueryInterface
     */
    function select(): QueryInterface;

    /**
     * @DESC         |插入
     *
     * 参数区：
     *
     * @param array $data
     * @return QueryInterface
     */
    function insert(array $data): QueryInterface;

    /**
     * @DESC         |删除
     *
     * 参数区：
     *
     * @return QueryInterface
     */
    function delete(): QueryInterface;

    /**
     * @DESC          | 查询结果集
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 21:09
     *
     * @param string $sql
     * @return QueryInterface
     */
    function query(string $sql): QueryInterface;

    /**
     * @DESC          # 附加的sql 用于复杂自定义的长sql 比如聚合函数的使用
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/24 22:06
     * 参数区：
     * @param string $additional_sql
     * @return QueryInterface
     */
    function additional(string $additional_sql): QueryInterface;

    /**
     * @DESC          | 查询最终的结果
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 21:09
     *
     * @param string $model_class
     * @return mixed
     */
    function fetch(string $model_class = ''): mixed;

    /**
     * @DESC          | 查询原始最终的结果
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 21:09
     *
     * @return array
     */
    function fetchOrigin(): array;

    /**
     * @DESC          # 清理特定条件
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/23 22:02
     * 参数区：
     * @param string $type 'wheres' | 'orders' | 'limit' | 'joins' | 'fields' | 'alias' | 'updates'|'table'
     * @return QueryInterface
     */
    function clear(string $type = ''): QueryInterface;

    /**
     * @DESC          # 清理特定条件
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/23 22:02
     * 参数区：
     * @param string $type 默认：清理除了表名、表别名、表主键外的查询缓存 'wheres' | 'orders' | 'limit' | 'joins' | 'fields' | 'alias' | 'updates'|'table'
     * @return QueryInterface
     */
    function clearQuery(string $type = ''): QueryInterface;

    /**
     * @DESC          # 重置所有
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 22:59
     * 参数区：
     * @return QueryInterface
     */
    function reset(): QueryInterface;

    /**
     * @DESC          # 开启事务
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/23 22:33
     * 参数区：
     * @return void
     */
    function beginTransaction(): void;

    /**
     * @DESC          # 事务回滚
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/23 22:33
     * 参数区：
     * @return void
     */
    function rollBack(): void;

    /**
     * @DESC          # 事务提交
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/23 22:33
     * 参数区：
     * @return void
     */
    function commit(): void;

    /**
     * 归档数据
     * @param string $period
     * @param string $field
     * @return $this
     */
    public function period(string $period, string $field = 'main_table.create_time'): static;

    /**
     * @DESC          # 读取最终的sql
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 21:42
     * 参数区：
     * @param bool $format
     * @return string
     */
    function getLastSql(bool $format = true): string;

    /**
     * @DESC          # 读取预编译sql
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 21:42
     * 参数区：
     * @param bool $format
     * @return string
     */
    function getPrepareSql(bool $format = true): string;
}