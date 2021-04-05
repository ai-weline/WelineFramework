<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;

use Weline\Framework\DataObject\TraitDataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;

abstract class Model extends \think\Model
{
    use TraitDataObject;

    private static DbManager $_db;
    private EventsManager $eventsManager;

    protected static function init()
    {
        // 设置事件
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
        // load之前事件
        $this->getEvenManager()->dispatch($this->getTable() . '_model_load_before', ['model' => $this]);
        if (!$value) {
            $pk = $this->getPk();
            $data = $this->db()->where("{$pk}='{$field_or_pk_value}'")->find();
        } else {
            $data = $this->db()->where("{$field_or_pk_value}='{$value}'")->find();
        }
        $this->setData($data);
        // load之之后事件
        $this->getEvenManager()->dispatch($this->getTable() . '_model_load_after', ['model' => $this]);
        return $this;
    }

    function save(array $data = [], string $sequence = null): bool
    {
        /**
         * 重载TP6 模型save方法 并加入事件机制
         */
        // save之前事件
        $this->getEvenManager()->dispatch($this->getTable() . '_model_save_before', ['model' => $this]);
        if ($data) {
            $this->setData($data);
        }
        $save_result = parent::save($this->getData(), $sequence);
        // save之前事件
        $this->getEvenManager()->dispatch($this->getTable() . '_model_save_before', ['model' => $this]);
        return $save_result;
    }

    /**
     * @DESC         |获取事件管理器
     *
     * 参数区：
     *
     * @return EventsManager
     * @throws \ReflectionException
     */
    protected function getEvenManager(): EventsManager
    {
        return ObjectManager::getInstance(EventsManager::class);
    }
}
