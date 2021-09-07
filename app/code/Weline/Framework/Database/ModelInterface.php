<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;


use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

interface ModelInterface
{

    /**
     * @DESC          # 提供表名 如果返回空值 则读取模型名称
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/25 23:45
     * 参数区：
     * @Suppress("unused")
     * @return string
     */
    function provideTable(): string;

    /**
     * @DESC          # 提供主键字段 【如果为空 默认为 id 】
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 20:58
     * 参数区：
     * @Suppress("unused")
     * @return string
     */
    function providePrimaryField(): string;


    /**
     * @DESC          # 设置 【开发模式下每次都会执行】
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/7 20:05
     * 参数区：
     * @Suppress("unused")
     */
    function setup(ModelSetup $setup, Context $context): void;

    /**
     * @DESC          # 模块更新时执行
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/7 20:07
     * 参数区：
     * @Suppress("unused")
     */
    function upgrade(ModelSetup $setup, Context $context): void;

    /**
     * @DESC          # 模块安装时执行
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/7 20:07
     * 参数区：
     * @Suppress("unused")
     */
    function install(ModelSetup $setup, Context $context): void;
}