<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;


interface ModelInterface
{

    /**
     * @DESC          # 设置表名
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/25 23:45
     * 参数区：
     * @param string $table
     * @return string
     */
    function setTable(string $table):string;

    /**
     * @DESC          # 设置模型字段
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/25 23:46
     * 参数区：
     * @param array $fields 示例：['id'=>]
     * @return void
     */
    function setFields(array $fields):void;
}