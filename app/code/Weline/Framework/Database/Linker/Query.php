<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Linker;


use PDO;
use PDOStatement;
use Weline\Framework\Database\Exception\DbException;
use Weline\Framework\Database\Linker\Query\QueryTrait;
use Weline\Framework\Database\Model;
use Weline\Framework\Exception\Core;
use function PHPUnit\Framework\isInstanceOf;

abstract class Query implements QueryInterface
{
    use QueryTrait;

    const attr_TABLE = 'table';
    const attr_TABLE_ALIAS = 'table_alias';
    const attr_JOIN = 'joins';

    const init_vars = [
        'table' => '',
        'table_alias' => 'main_table',
        'insert' => array(),
        'joins' => array(),
        'fields' => '*',
        'updates' => '',
        'wheres' => array(),
        'where_values' => array(),
        'limit' => '',
        'order' => array(),
        'sql' => '',
        'additional_sql' => '',
    ];

    private string $table = '';
    private string $table_alias = 'main_table';
    private array $insert = array();
    private array $joins = array();
    private string $fields = '*';
    private string $updates = '';
    private array $wheres = array();
    private array $wheres_values = array();
    private string $limit = '';
    private array $order = array();

    private ?PDOStatement $PDOStatement = null;
    private string $sql = '';
    private string $additional_sql = '';


    function table(string $table_name): QueryInterface
    {
        $this->table = $this->getTable($table_name);
        return $this;
    }

    function insert(array $data): QueryInterface
    {
        if (is_string(array_key_first($data))) {
            $this->insert[] = $data;
        } else {
            $this->insert = $data;
        }
        $fields = '(';
        foreach ($this->insert[0] as $field => $value) {
            $fields .= "`$field`,";
        }
        $fields = rtrim($fields, ',') . ')';
        $origin_fields = $this->fields;
        $this->fields = $fields;
        $this->prepareSql(__FUNCTION__);
        $this->fields = $origin_fields;
        return $this;
    }

    function update(array $data): QueryInterface
    {
        # TODO 处理批量更新
        foreach ($data as $field => $value) {
            if (is_int($field)) $this->exceptionHandle(__('请输入键值对数组，示例：%1', "['id'=>1,'name'=>'weline']"));
            $field = str_replace('`', '', $field);
            if (is_string($value)) {
                $value = "'$value'";
            }

            $this->updates .= "`$field`=$value,";
        }
        $this->updates = rtrim($this->updates, ',');
        $this->prepareSql(__FUNCTION__);
        return $this;
    }

    function alias(string $table_alias_name): QueryInterface
    {
        $this->table_alias = $table_alias_name;
        return $this;
    }

    function join(string $table, string $condition, string $type = 'left'): QueryInterface
    {
        if (1 === count(func_get_args())) $type = 'inner';
        $this->joins[] = [$table, $condition, $type];
        return $this;
    }

    function fields(string $fields): QueryInterface
    {
        $this->fields = $fields;
        return $this;
    }

    function where(array|string $field, mixed $value = null, string $condition = '=', string $where_logic = 'AND'): QueryInterface
    {
//        if (!DEV) {
//            $this->cache->get();// TODO 缓存
//        }
        if (is_array($field)) {
            foreach ($field as $f_key => $where_array) {
                # 处理两个元素数组
                if (2 === count($where_array)) {
                    $where_array[2] = $where_array[1];
                    $where_array[1] = '=';
                }
                # 检测条件数组 下角标 必须为数字
                $this->checkWhereArray($where_array, $f_key);
                # 检测条件数组 检测第二个元素必须是限定的 条件操作符
                $this->checkConditionString($where_array);
                if (isset($where_array[2]) && is_string($where_array[2])) {
                    $where_array[2] = "'{$where_array[2]}'";
                }
                $this->wheres[] = $where_array;
            }
        } else {
            if (is_string($value)) {
                $value = "'{$value}'";
            }
            if ($value) {
                $where_array = [$field, $condition, $value, $where_logic];
                # 检测条件数组 下角标 必须为数字
                $this->checkWhereArray($where_array, 0);
                # 检测条件数组 检测第二个元素必须是限定的 条件操作符
                $this->checkConditionString($where_array);
                $this->wheres[] = $where_array;
            } else {
                $this->wheres[] = [$field];
            }

        }
        return $this;

    }

    function limit($size, $offset = 0): QueryInterface
    {
        $this->limit = " LIMIT $offset,$size";
        return $this;
    }

    function order(string $fields, string $sort = 'DESC'): QueryInterface
    {
        $this->order[$fields] = $sort;
        return $this;
    }

    function find(): QueryInterface
    {
        $this->prepareSql(__FUNCTION__);
        return $this;
    }

    function select(): QueryInterface
    {
        $this->prepareSql(__FUNCTION__);
        return $this;
    }

    function delete(): QueryInterface
    {
        $this->prepareSql(__FUNCTION__);
        return $this;
    }

    function query($sql): QueryInterface
    {
        $this->PDOStatement = $this->linker->query($sql);
        return $this;
    }

    function additional(string $additional_sql): QueryInterface
    {
        $this->additional_sql = $additional_sql;
        return $this;
    }

    function fetch(): array|bool
    {
        $result = $this->PDOStatement->execute($this->wheres_values);
        $data = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        if ($result && $data) {
            return $data;
        }
        return $result;
    }

    /**
     * @DESC          # 获得最后的sql
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 23:06
     * 参数区：
     * @return string
     */
    public function getLastSql(): string
    {
        foreach ($this->wheres_values as $where_key => $wheres_value) {
            $this->sql = str_replace($where_key, (string)$wheres_value, $this->sql);
        }
        return \SqlFormatter::format($this->sql);
    }

    function clear(string $type = ''): QueryInterface
    {
        if ($type) {
            $attr_var_name = '' . $type;
            if (DEV && !isset(self::init_vars[$attr_var_name])) {
                $this->exceptionHandle(__('不支持的清理类型：%1 支持的初始化类型：%2', [$attr_var_name, var_export(self::init_vars, true)]));
            }
            $this->$attr_var_name = self::init_vars[$attr_var_name];
        } else {
            $this->reset();
        }
        return $this;
    }

    function reset(): QueryInterface
    {
        foreach (self::init_vars as $init_field => $init_var) {
            $this->$init_field = $init_var;
        }
        return $this;
    }

    function beginTransaction(): void
    {
        $this->linker->getLink()->beginTransaction();
    }

    function rollBack(): void
    {
        $this->linker->getLink()->rollBack();
    }

    function commit(): void
    {
        $this->linker->getLink()->rollBack();
    }
}