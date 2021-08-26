<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;

use JetBrains\PhpStorm\Pure;
use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Database\Cache\DbCache;
use Weline\Framework\Database\Exception\DbException;
use Weline\Framework\Database\Exception\ModelException;
use Weline\Framework\Database\Linker\QueryInterface;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;


abstract class AbstractModel extends DataObject
{
    protected string $table = '';
    private LinkerFactory $linker;
    private QueryInterface $query;
    private CacheInterface $cache;
    public string $suffix;
    public bool $have_suffix = false;
    public string $primary_key = 'id';

    private EventsManager $eventsManager;

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
        # 如果没有设置
        $this->linker = ObjectManager::getInstance(DbManager::class . 'Factory');
        $this->suffix = $this->linker->getConfigProvider()->getPrefix();
        $this->table = $this->processTable();
        $this->query = $this->getQuery();
        $this->cache = ObjectManager::getInstance(DbCache::class . 'Factory');
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
     * @return LinkerFactory
     */
    public function getLinker(): LinkerFactory
    {
        return $this->linker;
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
        return $this->linker->getQuery()->table($this->table)->identity($this->primary_key);
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
    public function load(int|string $field_or_pk_value, $value = null): mixed
    {
        // 清空之前的数据
        $this->unsetData();
        // 加载之前
        $this->load_before();
        // load之前事件
        $this->getEvenManager()->dispatch($this->getTable() . '_model_load_before', ['model' => $this]);
        if (empty($value)) {
            if ($data = $this->getQuery()->where($this->primary_key, $field_or_pk_value)->find()->fetch()) {
                $this->setData($data);
            }
        } else {
            $this->setData($this->getQuery()->where($field_or_pk_value, $value)->find()->fetch());
        }
        // load之之后事件
        $this->getEvenManager()->dispatch($this->getTable() . '_model_load_after', ['model' => $this]);
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
        $this->getEvenManager()->dispatch($this->getTable() . '_model_save_before', ['model' => $this]);
        if ($data) {
            $this->setData($data);
        }
        $this->query->beginTransaction();
        try {
            // 保存前才检查 是否已经存在
            if ($old_id) {
                $this->query->clear('wheres');
                $save_result = $this->query/*->where($this->primary_key, $old_id)*/->update($data)->fetch();
            } else {
                $save_result = $this->query->insert($data)->fetch();
            }
            $this->query->commit();
        } catch (\Exception $exception) {
            $this->query->rollBack();
            throw new ModelException(__('模型保存数据出错：%1', $exception->getMessage()));
        }

        p($save_result);

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
            throw new DbException(__('数据库链接异常：%1'), $e->getMessage());
        } catch (Exception $e) {
        }
    }

    public function find(int|string $field_or_pk_value, $value = null)
    {
        return $this->load($field_or_pk_value, $value = null);
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
