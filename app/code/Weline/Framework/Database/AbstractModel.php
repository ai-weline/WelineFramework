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
use Weline\Framework\Database\Exception\ModelException;
use Weline\Framework\Database\Linker\QueryInterface;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Exception\Core;
use Weline\Framework\Manager\ObjectManager;

/**
 * Class AbstractModel
 * @method QueryInterface identity(string $field)
 * @method QueryInterface table(string $table_name)
 * @method QueryInterface alias(string $table_alias_name)
 * @method QueryInterface update(array $data, string $condition_field = 'id')
 * @method QueryInterface fields(string $fields)
 * @method QueryInterface join(string $table, string $condition, string $type = 'left')
 * @method QueryInterface where(array|string $field, mixed $value = null, string $condition = '=', string $where_logic = 'AND')
 * @method QueryInterface limit(int $size, int $offset = 0)
 * @method QueryInterface order(string $fields, string $sort = 'ASC')
 * @method QueryInterface find()
 * @method QueryInterface select()
 * @method QueryInterface insert(array $data)
 * @method QueryInterface query(string $sql)
 * @method QueryInterface additional(string $additional_sql)
 * @method QueryInterface clear(string $type = '')
 * @method QueryInterface clearQuery(string $type = '')
 *
 * @method QueryInterface fetch()
 * @method QueryInterface reset()
 * @method QueryInterface beginTransaction()
 * @method QueryInterface rollBack()
 * @method QueryInterface commit()
 * @method QueryInterface getLastSql()
 * @method QueryInterface getPrepareSql()
 * @package Weline\Framework\Database
 */
abstract class AbstractModel extends DataObject
{
    protected string $table = '';
    private LinkerFactory $linker;
    private CacheInterface $cache;
    public string $suffix;
    public bool $have_suffix = true;
    public string $primary_key = 'id';
    public array $fields = [];

    /**
     * @DESC         |初始化连接、缓存、表前缀 读取模型自身表名字等
     *
     * 参数区：
     *
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     */
    public function __init()
    {
        $this->linker = ObjectManager::getInstance(DbManager::class . 'Factory');
        $this->suffix = $this->linker->getConfigProvider()->getPrefix();
        $this->cache = ObjectManager::getInstance(DbCache::class . 'Factory');
        # 模型属性
        $this->table = $this->providerTable()?:$this->processTable();
        $this->primary_key = $this->providerPrimaryField() ?: $this->primary_key;
        $this->fields = $this->providerFields() ?: $this->fields;
    }

    /**
     * @DESC          # 处理表名 存在表名则不处理
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 20:32
     * 参数区：
     * @return string
     */
    protected function processTable(): string
    {
        if (!$this->table) {
            $class_file_name_arr = explode('\\', $this::class);
            $class_file_name = array_pop($class_file_name_arr);
            $table_name = str_replace('Model', '', $class_file_name);
            $this->table = ($this->have_suffix ? $this->suffix : '') . strtolower(implode('_', m_split_by_capital(lcfirst($table_name))));
        }
        return $this->table;
    }

    /**
     * @DESC         |获取数据库基类
     *
     * 参数区：
     *
     * @return QueryInterface
     * @throws Exception
     * @throws \ReflectionException
     */
    public function getQuery(): QueryInterface
    {
        return $this->linker->getQuery()->clearQuery()->table($this->table)->identity($this->primary_key);
    }

    /**
     * @DESC          # 读取模型的主键字段值
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 21:54
     * 参数区：
     */
    function getId()
    {
        return $this->getData($this->primary_key);
    }

    /**
     * @DESC          # 设置模型的主键字段值
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 21:54
     * 参数区：
     */
    function setId($primary_id): AbstractModel
    {
        return $this->setData($this->primary_key, $primary_id);
    }

    /**
     * @DESC         |读取当前模型的数据
     * 如果只给一个参数则当做读取主键的值
     * 如果给定一个字段，那么必填第二个参数，当做这个字段的值
     *
     * 参数区：
     *
     * @param int|string $field_or_pk_value 字段或者主键的值
     * @param null $value 字段的值，只读取主键就不填
     * @return mixed
     * @throws \ReflectionException
     * @throws \Weline\Framework\Exception\Core
     */
    public function load(int|string $field_or_pk_value, $value = null): AbstractModel
    {
        // 清空之前的数据
        $this->unsetData();
        // 加载之前
        $this->load_before();
        // load之前事件
        $this->getEvenManager()->dispatch($this->processTable() . '_model_load_before', ['model' => $this]);
        if (empty($value)) {
            $data = $this->getQuery()->where($this->primary_key, $field_or_pk_value)->find()->fetch();
        } else {
            $data = $this->getQuery()->where($field_or_pk_value, $value)->find()->fetch();
        }
        if (is_array($data)) $this->setData($data);
        // load之之后事件
        $this->getEvenManager()->dispatch($this->processTable() . '_model_load_after', ['model' => $this]);
        // 加载之后
        $this->load_after();
        return $this;
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
        $old_id = $this->getId();
        // 保存前
        $this->save_before();
        /**
         * 重载TP6 模型save方法 并加入事件机制
         */
        // save之前事件
        $this->getEvenManager()->dispatch($this->processTable() . '_model_save_before', ['model' => $this]);
        if ($data) {
            $this->setData($data);
        }
        $this->getQuery()->beginTransaction();
        try {
            // 保存前才检查 是否已经存在
            if ($old_id == $this->getId()) {
                $save_result = $this->getQuery()->where($this->primary_key, $old_id)->update($this->getData())->fetch();
            } else {
                $save_result = $this->getQuery()->insert($data)->fetch();
            }
            $this->getQuery()->commit();
        } catch (\Exception $exception) {
            $this->getQuery()->rollBack();
            throw new ModelException(__('模型保存数据出错：%1 预编译SQL: %2 执行SQL: %3 ', [$exception->getMessage(), $this->getQuery()->getPrepareSql(), $this->getQuery()->getLastSql()]));
        }

        // save之后事件
        $this->getEvenManager()->dispatch($this->processTable() . '_model_save_after', ['model' => $this]);
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
     * @DESC          # 获取事件管理器
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/30 21:12
     * 参数区：
     * @return EventsManager
     * @throws Exception
     * @throws \ReflectionException
     */
    protected function getEvenManager(): EventsManager
    {
        return ObjectManager::getInstance(EventsManager::class);
    }

    /**
     * @DESC          # 删除 模型中 对应主键值的条目
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/30 21:08
     * 参数区：
     * @return $this
     * @throws Core
     * @throws Exception
     * @throws \ReflectionException
     */
    function delete(): AbstractModel
    {
        // 加载之前
        $this->delete_before();
        // load之前事件
        $this->getEvenManager()->dispatch($this->processTable() . '_model_delete_before', ['model' => $this]);
        $this->getQuery()->where($this->primary_key, $this->getId())->delete()->fetch();
        $this->clearData();
        // load之之后事件
        $this->getEvenManager()->dispatch($this->processTable() . '_model_delete_after', ['model' => $this]);
        // 加载之后
        $this->delete_after();
        return $this;
    }

    function delete_before()
    {
    }

    function delete_after()
    {
    }

    /**
     * @DESC          # 清空数据
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/30 21:04
     * 参数区：
     * @return AbstractModel
     */
    function clearData(): AbstractModel
    {
        return $this->setData(array());
    }

    /**
     * @DESC         |访问不存在的方法时，默认为查询
     *
     * 参数区：
     *
     * @param $method
     * @param $args
     * @return array|bool|mixed|string|AbstractModel|null
     * @throws \Weline\Framework\Exception\Core
     */
    function __call($method, $args)
    {
        // 判断是查询方法
        $cache_key = 'query_methods';
        $query_funcs = $this->cache->get($cache_key);
        if (empty($query_funcs)) {
            $query_funcs = get_class_methods(QueryInterface::class);
            unset($query_funcs['fetch']);
            $this->cache->set($cache_key, $query_funcs);
        }
        // 模型查询
        if (in_array($method, $query_funcs)) {
            return $this->linker->getQuery()->$method(... $args);
        }
        /**
         * 重载方法
         */
        return parent::__call($method, $args);
    }

    /**
     * @DESC          # 是否有前缀
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 20:56
     * 参数区：
     * @param bool $have_suffix
     * @return AbstractModel
     */
    protected function setHaveSuffix(bool $have_suffix): static
    {
        $this->have_suffix = $have_suffix;
        return $this;
    }
}
