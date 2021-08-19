<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;

use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Database\Cache\DbCache;
use Weline\Framework\Database\Exception\DbException;
use Weline\Framework\Database\test\Linker\QueryInterface;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;

class Model extends DataObject
{
    private Linker $linker;
    private CacheInterface $cache;
    private string $suffix;
    private array $query_funcs;

    private EventsManager $eventsManager;


    /**
     * @DESC         |初始化连接、缓存、表前缀
     *
     * 参数区：
     *
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     */
    public function __init()
    {
        $this->linker = ObjectManager::getInstance(DbManager::class . 'Factory');
        $this->cache = ObjectManager::getInstance(DbCache::class . 'Factory');
        $this->suffix = $this->getSuffix() . $this->suffix;
    }

    /**
     * @DESC         |获取数据库基类
     *
     * 参数区：
     *
     * @return Linker
     */
    public function getLinker(): Linker
    {
        return $this->linker;
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
     */
    public function load(string $field_or_pk_value, $value = null)
    {
        // 清空之前的数据
        $this->unsetData();
        // 加载之前
        $this->load_before();
        // load之前事件
        $this->getEvenManager()->dispatch($this->getTable() . '_model_load_before', ['model' => $this]);
        if (!$value) {
            $pk = $this->getPk();
            $model = $this->where("`{$pk}`=:pkv", ['pkv' => $field_or_pk_value])->find();
        } else {
            $model = $this->where("`{$field_or_pk_value}`=:fv", ['fv' => $value])->find();
        }
//        // 有数据就回填到对象
        if ($model && $model->getId()) {
            $this->data($model->getData());
            $this->setData($model->getData());
        }
        // load之之后事件
        $this->getEvenManager()->dispatch($this->getTable() . '_model_load_after', ['model' => $this]);

        // 加载之后
        $this->load_after();

        return $model;
    }


    /**
     * @DESC         |载入前
     *
     * 参数区：
     */
    public function load_before()
    {
    }

    /**
     * @DESC         |载入后
     *
     * 参数区：
     */
    public function load_after()
    {
    }

    /**
     * @DESC         |保存方法
     *
     * 参数区：
     *
     * @param array $data
     * @param string|null $sequence
     * @return bool
     * @throws \Weline\Framework\Exception\Core
     * @throws \ReflectionException
     */
    public function save(array $data = [], string $sequence = null): bool
    {
        // 保存前
        $this->save_before();
        /**
         * 重载TP6 模型save方法 并加入事件机制
         */
        // save之前事件
        $this->getEvenManager()->dispatch($this->getTable() . '_model_save_before', ['model' => $this]);
        if ($data) {
            $this->setData($data);
        }
        // 保存前才检查 是否已经存在
        if ($this->getId()) {
            $this->exists(true);
        } else {
            $this->exists(false);
        }

        $save_result = parent::save($this->getData(), $sequence);
        // save之后事件
        $this->getEvenManager()->dispatch($this->getTable() . '_model_save_after', ['model' => $this]);

        // 保存后
        $this->save_after();

        return $save_result;
    }

    public function save_before()
    {
    }

    public function save_after()
    {
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

    /**
     * @DESC         |获得数据库管理器
     *
     * 参数区：
     *
     * @return DbManager
     * @throws \ReflectionException
     */
    public function getDbManager(): DbManager
    {
        try {
            return ObjectManager::getInstance(DbManager::class);
        } catch (\ReflectionException $e) {
            throw new DbException(__('数据库链接异常：%1'),$e->getMessage());
        } catch (Exception $e) {
        }
    }

    public function find($data = null)
    {
        $find_data = parent::find($data);
        $this->setData($find_data);
        return $find_data;
    }

    /**
     * @DESC         |访问不存在的方法时，默认为查询
     *
     * 参数区：
     *
     * @param $method
     * @param $args
     * @return array|bool|mixed|string|Model|null
     * @throws \Weline\Framework\Exception\Core
     */
    function __call($method, $args)
    {
        // 判断是查询方法
        $cache_key = 'query_methods';
        $query_funcs = $this->cache->get($cache_key);
        if (empty($query_funcs)) {
            $query_funcs = get_class_methods(QueryInterface::class);
            $this->cache->set($cache_key, $query_funcs);
        }
        // TODO 模型查询
        if (in_array($method, $query_funcs)) {
            return $this->linker->getQuery()->$method(...$query_funcs);
        }
        /**
         * 重载方法
         */
        return parent::__call($method, $args);
    }
}
