<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;

use Weline\Framework\DataObject\TraitDataObject;

abstract class Model extends \think\Model
{
    use TraitDataObject;

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

    /**
     * @DESC         |读取当前模型的数据
     * 如果只给一个参数则当做读取主键的值
     * 如果给定一个字段，那么必填第二个参数，当做这个字段的值
     *
     * 参数区：
     *
     * @param string $field_or_pk_value 字段或者主键的值
     * @param null $value 字段的值，只读取主键就不填
     * @return $this
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function load(string $field_or_pk_value, $value = null)
    {
        if (!$value) {
            $pk = $this->getPk();
            $data = $this->db()->where("{$pk}='{$field_or_pk_value}'")->find();
        } else {
            $data = $this->db()->where("{$field_or_pk_value}='{$value}'")->find();
        }
        $this->setData($data);
        return $this;
    }
}
