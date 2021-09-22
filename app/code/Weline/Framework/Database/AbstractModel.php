<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;

use Weline\Framework\App\Exception;
use Weline\Framework\Database\Api\Linker\QueryInterface;
use Weline\Framework\Database\Exception\ModelException;
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
    const fetch_data = 'fetch_data';
    protected string $table = '';
    protected string $origin_table_name = '';
    private LinkerFactory $linker;
    public string $suffix = '';
    public string $primary_key = 'id';
    public array $fields = [];

    private bool $force_check_flag = false;

    private QueryInterface $keep_query;

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
        # 类属性
        $this->linker = ObjectManager::getInstance(DbManager::class . 'Factory');
        $this->suffix = $this->linker->getConfigProvider()->getPrefix() ?: '';
        # 模型属性
        $this->table = $this->provideTable() ?: $this->processTable();
        if (empty($this->origin_table_name)) $this->origin_table_name = $this->provideTable();
        $this->primary_key = $this->providePrimaryField() ?: $this->primary_key;
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
            $this->origin_table_name = $this->suffix . strtolower(implode('_', m_split_by_capital(lcfirst($table_name))));
            $this->table = "`{$this->linker->getConfigProvider()->getDatabase()}`.`{$this->origin_table_name}`";
        }
        return $this->table;
    }

    /**
     * @DESC          # 读取表名
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/3 20:14
     * 参数区：
     * @return string
     */
    function getTable(): string
    {
        return $this->processTable();
    }

    /**
     * @DESC          # 读取原始表名
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/3 20:14
     * 参数区：
     * @return string
     */
    function getOriginTableName(): string
    {
        $this->processTable();
        return $this->origin_table_name;
    }

    /**
     * @DESC         |获取数据库基类
     *
     * 参数区：
     *
     * @param bool $keep_condition
     * @return QueryInterface
     * @throws Exception
     * @throws \ReflectionException
     */
    public function getQuery(bool $keep_condition = false): QueryInterface
    {
        if ($keep_condition) {
            return $this->linker->getQuery()->table($this->getOriginTableName())->identity($this->primary_key);
        }
        return $this->linker->getQuery()->clearQuery()->table($this->getOriginTableName())->identity($this->primary_key);
    }

    /**
     * @DESC          # 设置保存查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/21 21:34
     * 参数区：
     * @param QueryInterface $query
     * @return $this
     */
    private function setKeepQuery(QueryInterface $query): static
    {
        return $this->setData('keep_query', $query);
    }

    /**
     * @DESC          # 清理保存查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/21 21:35
     * 参数区：
     * @return AbstractModel|DataObject
     */
    private function clearKeepQuery(): DataObject|AbstractModel
    {
        return $this->unsetData('keep_query');
    }

    /**
     * @DESC          # 获取保存查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/21 21:35
     * 参数区：
     * @return mixed
     */
    function getKeepQuery(): mixed
    {
        return $this->getData('keep_query');
    }

    /**
     * @DESC          # 获取链接
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/3 19:59
     * 参数区：
     * @return LinkerFactory
     */
    function getLink(): LinkerFactory
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
     * @param int|string $field_or_pk_value 字段或者主键的值
     * @param null $value 字段的值，只读取主键就不填
     * @return mixed
     * @throws \ReflectionException
     * @throws \Weline\Framework\Exception\Core
     */
    public function load(int|string $field_or_pk_value, $value = null): AbstractModel
    {
        // 加载之前
        $this->load_before();
        // 清空之前的数据
        $this->clearDataObject();
        // load之前事件
        $this->getEvenManager()->dispatch($this->getOriginTableName() . '_model_load_before', ['model' => $this]);
        if (is_null($value)) {
            $data = $this->getQuery()->where($this->primary_key, $field_or_pk_value)->find()->fetch();
        } else {
            $data = $this->getQuery()->where($field_or_pk_value, $value)->find()->fetch();
        }
        if (is_array($data)) $this->setData($data);
        // load之之后事件
        $this->getEvenManager()->dispatch($this->getOriginTableName() . '_model_load_after', ['model' => $this]);
        // 加载之后
        $this->load_after();
        return $this;
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
        $save_result = false;
        try {
            // 保存前才检查 是否已经存在
            if (!$this->force_check_flag && $old_id && $old_id == $this->getId()) {
                $save_result = $this->getQuery()->where($this->primary_key, $old_id)->update($this->getData())->fetch();
            } else {
                if ($this->force_check_flag) {
                    if ($data = $this->getQuery()->where($this->primary_key, $old_id)->find()->fetch()) {
                        # 数据相同则不更新
                        if ($data != $this->getData()) {
                            $save_result = $this->getQuery()->where($this->primary_key, $old_id)->update($this->getData())->fetch();
                        }
                    } else {
                        $save_result = $this->getQuery()->insert($this->getData())->fetch();
                        $save_result = array_shift($save_result)['LAST_INSERT_ID()'];
                        $this->setData($this->primary_key, $save_result);
                    }
                } else {
                    $save_result = $this->getQuery()->insert($this->getData())->fetch();
                    $save_result = array_shift($save_result)['LAST_INSERT_ID()'];
                    $this->setData($this->primary_key, $save_result);
                }
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
     * @DESC          # 【强制检测】 true:强制查询数据库，检测数据是否存在 不存在则插入记录 false:检测当前模型是否存在主键，存在则更新，不存在则插入
     *                # 【原因】 如果主键非ID自增键时，因为主键就是数据，无法检测，只能先查询后操作，遇到此类情况时使用此函数
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/14 22:49
     * 参数区：
     * @param bool $force_check_flag
     * @return AbstractModel
     */
    function forceCheck(bool $force_check_flag = true): AbstractModel
    {
        $this->force_check_flag = $force_check_flag;
        return $this;
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
     * @DESC         |访问不存在的方法时，默认为查询
     *
     * 参数区：
     *
     * @param $method
     * @param $args
     * @return array|bool|mixed|string|AbstractModel|null
     * @throws \Weline\Framework\Exception\Core
     * @throws \ReflectionException
     */
    function __call($method, $args)
    {
        // 模型查询
        if (in_array($method, get_class_methods(QueryInterface::class))) {
            # 非链式操作的Fetch
            $is_fetch = false;
            # 拦截fetch操作 注入返回的模型
            if ('fetch' === $method) {
                $args[] = $this::class;
                $is_fetch = true;
            }
            $query_data = $this->getQuery(true)->$method(... $args);
            # 拦截fetch返回的数据注入模型
            if ($is_fetch) {
                $this->getQuery()->clearQuery();
                $this->setFetchData($query_data);
            }

            return $query_data;
        }
        /**
         * 重载方法
         */
        return parent::__call($method, $args);
    }

    /**
     * @DESC          # 设置取得的珊瑚橘
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/22 19:32
     * 参数区：
     * @param AbstractModel[] $value
     */
    function setFetchData(array $value):self
    {
        return $this->setData(self::fetch_data, $value);
    }

    function setData($key, $value = null): static
    {
        $this->set_data_before($key, $value);
        parent::setData($key, $value);
        $this->set_data_after($key, $value);
        return $this;
    }

    function set_data_before(string|array $key, mixed $value = null)
    {

    }

    function set_data_after(string|array $key, mixed $value = null)
    {

    }
}
