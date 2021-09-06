<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;


use Weline\Framework\Database\Db\Ddl\Create;
use Weline\Framework\Setup\Db\ModelSetup;
use Weline\Framework\Setup\Db\Setup;

interface ModelInterface
{

    /**
     * @DESC          # 提供表名 如果返回空值 则读取模型名称
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/25 23:45
     * 参数区：
     * @return string
     */
    function provideTable():string;

    /**
     * @DESC          # 提供主键字段 【如果为空 默认为 id 】
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 20:58
     * 参数区：
     * @return string
     */
    function providePrimaryField():string;

    /**
     * @DESC          # 开发升级方法 【仅有开发模式会触发】
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/3 21:12
     * 参数区：
     * @param ModelSetup $setup
     * @return void
     */
    function devSetup(ModelSetup $setup):void;

    /** TODO 持续完成模块安装 模块升级 模块更新 模型目前只有ModelSetup 其余还需要开发
     * @DESC          # 安装执行函数
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/7 0:01
     * 参数区：
     * @param ModelSetup $setup
     */
    function install(ModelSetup $setup):void;
}