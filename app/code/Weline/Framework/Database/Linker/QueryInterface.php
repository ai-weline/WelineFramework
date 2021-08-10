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


use Weline\Framework\Database\Model;

interface QueryInterface
{
    /**
     * @DESC         |where查询
     *
     * 参数区：
     *
     * @param string $where_str
     * @return mixed
     */
    function where(string $where_str):Query;

    /**
     * @DESC         |选择
     *
     * 参数区：
     *
     * @return Model []
     */
    function select():array;

    /**
     * @DESC         |连接查询
     *
     * 参数区：
     *
     * @param string $join_str
     * @return Query
     */
    function join(string $join_str):Query;

    /**
     * @DESC         |插入
     *
     * 参数区：
     *
     * @param string|array $data
     * @return bool
     */
    function insert(string|array $data):bool;

    /**
     * @DESC         |删除
     *
     * 参数区：
     *
     * @return bool
     */
    function delete():bool;

    /**
     * @return mixed
     */
    function query(): mixed;
}