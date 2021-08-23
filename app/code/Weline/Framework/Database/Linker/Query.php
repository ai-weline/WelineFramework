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

    const init_vars = [
        '_table' => '',
        '_table_alias' => 'main_table',
        '_joins' => array(),
        '_fields' => '*',
        '_updates' => '',
        '_wheres' => array(),
        '_where_values' => array(),
        '_limit' => '',
        '_order' => array(),
        'sql' => '',
    ];

    private string $_table = '';
    private string $_table_alias = 'main_table';
    private array $_joins = [];
    private string $_fields = '*';
    private string $_updates = '';
    private array $_wheres = [];
    private array $_wheres_values = [];
    private string $_limit = '';
    private array $_order = [];

    private ?PDOStatement $PDOStatement = null;
    private string $sql = '';


    function table(string $table_name): Query
    {
        $this->_table = $this->getTable($table_name);
        return $this;
    }

    function update(array $data, $type = 'replace'): Query
    {
        # 处理批量更新
        foreach ($data as $field => $value) {
            if (is_int($field)) $this->exceptionHandle(__('请输入键值对数组，示例：%1', "['id'=>1,'name'=>'weline']"));
            $field = str_replace('`', '', $field);
            if (is_string($value)) {
                $value = "'$value'";
            }

            $this->_updates .= "`$field`=$value,";
        }
        $this->_updates = rtrim($this->_updates, ',');
        $this->prepareSql(__FUNCTION__);
        return $this;
    }

    function alias(string $table_alias_name): Query
    {
        $this->_table_alias = $table_alias_name;
        return $this;
    }

    function join(string $table, string $condition, string $type = 'INNER'): Query
    {
        $this->_joins[] = [$table, $condition, $type];
        return $this;
    }

    function fields(string $fields): Query
    {
        $this->_fields = $fields;
        return $this;
    }

    function where(array|string $field, mixed $value = null, string $condition = '=', string $where_logic = 'AND'): Query
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
                $this->_wheres[] = $where_array;
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
                $this->_wheres[] = $where_array;
            } else {
                $this->_wheres[] = [$field];
            }

        }
        return $this;

    }

    function limit($size, $offset = 0): Query
    {
        $this->_limit = " LIMIT $offset,$size";
        return $this;
    }

    function order(string $fields, string $sort = 'DESC'): Query
    {
        $this->_order[] = " ORDER BY `{$fields}` {$sort}";
        return $this;
    }

    function find(): Query
    {
        $this->prepareSql(__FUNCTION__);
        return $this;
    }

    function select(): array
    {
        // TODO: Implement select() method.
    }

    function query($sql): Query
    {
        $this->PDOStatement = $this->linker->query($sql);
        return $this;
    }

    function fetch($return_origin_result = false): array|bool
    {
        $result = $this->PDOStatement->execute($this->_wheres_values);
        if (is_bool($result)) {
            return $result;
        }
        return $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
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
        foreach ($this->_wheres_values as $where_key => $wheres_value) {
            $this->sql = str_replace($where_key, (string)$wheres_value, $this->sql);
        }
        return \SqlFormatter::format($this->sql);
    }

    function clear(string $type = 'wheres'): Query
    {
        $attr_var_name = '_1' . $type;
        if (!isset(self::init_vars[$attr_var_name])) {
            $this->exceptionHandle(__('不支持的清理类型：%1 支持的初始化类型：%2', [$attr_var_name, var_export(self::init_vars, true)]));
        }
        $this->$attr_var_name = self::init_vars[$attr_var_name];
        return $this;
    }

    function reset(): Query
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