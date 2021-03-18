<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;

abstract class Model extends \think\Model
{
    private static DbManager $_db;

    protected static function init()
    {
        self::$db = self::$_db = new DbManager();
        /**
         * 重载方法
         */
        parent::init();
    }

    /**
     * @DESC         |获取数据库基类
     *
     * 参数区：
     *
     * @return \think\DbManager
     */
    public function getDb()
    {
        return self::$db;
    }
}
