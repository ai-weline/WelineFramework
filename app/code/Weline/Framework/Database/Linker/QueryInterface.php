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

namespace Weline\Framework\Database\Linker;


use Weline\Framework\Database\Linker;
use Weline\Framework\Database\Model;

interface QueryInterface
{

    /**
     * @DESC          # 表名设置
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 23:01
     * 参数区：
     * @param string $table_name
     * @return mixed
     */
    function table(string $table_name): Query;

    /**
     * @DESC          # 表名别名
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/17 23:25
     * 参数区：
     * @param string $table_alias_name
     * @return Query
     */
    function alias(string $table_alias_name): Query;

    /**
     * @DESC          # 更新
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 0:20
     * 参数区：
     * @param array $data
     * @return mixed
     */
    function update(array $data): mixed;// TODO 更新方式

    /**
     * @DESC          # 表名设置
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 23:01
     * 参数区：
     * @param string $fields 示例：a.id,a.name,b.role_id,b.rule_name
     * @return mixed
     */
    function fields(string $fields): Query;

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
     * @return Query
     */
    function join(string $table, string $condition, string $type): Query;

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
     * @return $this
     */
    function where(array|string $field, mixed $value = null, string $condition = '=', string $where_logic = 'AND'): Query;

    /**
     * @DESC          # 限制查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 22:44
     * 参数区：
     * @param int $size
     * @param int $offset
     * @return Query
     */
    function limit(int $size, int $offset = 0): Query;

    /**
     * @DESC          # 方法描述
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 22:46
     * 参数区：
     * @param string $fields
     * @param string $sort
     * @return Query
     */
    function order(string $fields, string $sort = 'DESC'): Query;

    /**
     * @DESC          # 仅查找一个
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/17 23:15
     * 参数区：
     * @return mixed
     */
    function find(): mixed;

    /**
     * @DESC         |选择
     *
     * 参数区：
     *
     * @return Model []
     */
    function select(): array;

    /**
     * @DESC         |插入
     *
     * 参数区：
     *
     * @param string|array $data
     * @return bool
     */
    function insert(string|array $data): bool;

    /**
     * @DESC         |删除
     *
     * 参数区：
     *
     * @return bool
     */
    function delete(): bool;

    /**
     * @DESC          | 查询结果集
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 21:09
     *
     * @param $sql
     * @return $this
     */
    function query($sql): static;

    /**
     * @DESC          | 查询最终的结果
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 21:09
     *
     * @return array
     */
    function fetch(): array;

    /**
     * @DESC          # 方法描述
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 22:59
     * 参数区：
     * @return $this
     */
    function clearQuery(): static;

}