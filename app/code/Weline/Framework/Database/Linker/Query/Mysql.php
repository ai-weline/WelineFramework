<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/6/21
 * 时间：11:45
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Database\Linker\Query;



use Weline\Framework\Database\Linker\Query;

class Mysql extends Query
{
    function select(): array
    {


    }

    function join(string $join_str): Query
    {
        // TODO: Implement join() method.
    }

    function insert(array|string $data): bool
    {
        // TODO: Implement insert() method.
    }

    function delete(): bool
    {
        // TODO: Implement delete() method.
    }

    function where(string $where_str): Query
    {
        // TODO: Implement where() method.
    }

    function query(): mixed
    {
        // TODO: Implement query() method.
    }
}