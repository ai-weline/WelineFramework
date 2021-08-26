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
     * @DESC          # 返回表名 如果返回空值 则读取模型名称
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/25 23:45
     * 参数区：
     * @return string
     */
    function getTable():string;

    /**
     * @DESC          # 方法描述
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 20:58
     * 参数区：
     * @return string
     */
    function getPrimaryKey():string;

    /**
     * @DESC          # 设置模型字段
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 21:17
     * 参数区：
     * @return array
     */
    function getFields():array;
}