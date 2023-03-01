<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Connection;

use PDO;
use PDOStatement;
use Weline\Framework\Database\AbstractModel;
use Weline\Framework\Database\Api\Connection\QueryInterface;
use Weline\Framework\Database\Exception\DbException;
use Weline\Framework\Database\Connection\Query\QueryTrait;
use Weline\Framework\Database\Exception\SqlParserException;
use Weline\Framework\Database\Model;
use Weline\Framework\Manager\ObjectManager;

use function DeepCopy\deep_copy;

abstract class Query implements QueryInterface
{
    use QueryTrait;

    public string $identity_field = 'id';
    public string $table = '';
    public string $table_alias = 'main_table';
    public array $insert = [];
    public string $exist_update_sql = '';
    public array $joins = [];
    public string $fields = '*';
    public array $single_updates = [];
    public array $updates = [];
    public array $wheres = [];
    public array $bound_values = [];
    public string $limit = '';
    public array $order = [];
    public string $group_by = '';
    public string $having = '';

    private ?PDOStatement $PDOStatement = null;
    public string $sql = '';
    public string $additional_sql = '';

    public string $fetch_type = '';

    public array $pagination = ['page' => 1, 'pageSize' => 20, 'totalSize' => 0, 'lastPage' => 0];


    public function identity(string $field): QueryInterface
    {
        $this->identity_field = $field;
        return $this;
    }

    public function table(string $table_name): QueryInterface
    {
        $this->table = $this->getTable($table_name);
        return $this;
    }

    public function insert(array $data, array|string $exist_update_fields = []): QueryInterface
    {
        if ($exist_update_fields) {
            $this->exist_update_sql = 'ON DUPLICATE KEY UPDATE ';
            if (is_string($exist_update_fields)) {
                $this->exist_update_sql .= "`$exist_update_fields`=VALUES(`$exist_update_fields`),";
            } else {
                foreach ($exist_update_fields as $field) {
                    $this->exist_update_sql .= "`$field`=VALUES(`$field`),";
                }
            }
            $this->exist_update_sql = trim($this->exist_update_sql, ',');
        }
        if (is_string(array_key_first($data))) {
            $this->insert[] = $data;
        } else {
            $this->insert = $data;
        }
        $fields = '(';
        if (count($this->insert)) {
            $first_insert = $this->insert[array_key_first($this->insert)];
            foreach ($first_insert as $field => $value) {
                $fields .= "`$field`,";
            }
        }
        $fields           = rtrim($fields, ',') . ')';
        $origin_fields    = $this->fields;
        $this->fields     = $fields;
        $this->fetch_type = __FUNCTION__;
        $this->prepareSql(__FUNCTION__);
        $this->fields = $origin_fields;
        return $this;
    }

    public function update(array|string $field, int|string $value_or_condition_field = 'id'): QueryInterface
    {
        if (empty($field)) {
            throw new DbException(__('更新异常，不可更新空数据！'));
        }
        # 单条记录更新
        if (is_string($field)) {
            $this->single_updates[$field] = $value_or_condition_field;
        } else {
            // 设置数据更新依赖条件主键
            if ($this->identity_field !== $value_or_condition_field) {
                $this->identity_field = $value_or_condition_field;
            }
            if (is_string(array_key_first($field))) {
                $this->updates[] = $field;
            } else {
                $this->updates = $field;
            }
        }
        $this->fetch_type = __FUNCTION__;
        $this->prepareSql(__FUNCTION__);
        return $this;
    }

    public function alias(string $table_alias_name): QueryInterface
    {
        $this->table_alias = $table_alias_name;
        return $this;
    }

    public function join(string $table, string $condition, string $type = 'left'): QueryInterface
    {
        if (1 === count(func_get_args())) {
            $type = 'inner';
        }
        $this->joins[] = [$table, $condition, $type];
        return $this;
    }

    public function fields(string $fields): QueryInterface
    {
        if ($this->fields === '*') {
            $this->fields = $fields;
        } else {
            $this->fields = $fields .',' . $this->fields;
            $fields       = explode(',', $this->fields);
            $fields       = array_unique($fields);
            $this->fields = implode(',', $fields);
        }
        return $this;
    }

    public function where(array|string $field, mixed $value = null, string $condition = '=', string $where_logic = 'AND'): QueryInterface
    {
        if (is_array($field)) {
            foreach ($field as $f_key => $where_array) {
                if (!is_array($where_array)) {
                    $value          = $where_array;
                    $where_array    = [];
                    $where_array[0] = $f_key;
                    $where_array[1] = '=';
                    $where_array[2] = $value;
                    $where_array[3] = $where_logic;
                } elseif (2 === count($where_array)) {# 处理两个元素数组
                    $where_array[2] = $where_array[1];
                    $where_array[1] = '=';
                }


                # 检测条件数组 下角标 必须为数字
                $this->checkWhereArray($where_array, $f_key);
                # 检测条件数组 检测第二个元素必须是限定的 条件操作符
                $this->checkConditionString($where_array);
                $this->wheres[] = $where_array;
            }
        } else {
//            if ($value) {
//                $where_array = [$field, $condition, $value, $where_logic];
//                # 检测条件数组 下角标 必须为数字
//                $this->checkWhereArray($where_array, 0);
//                # 检测条件数组 检测第二个元素必须是限定的 条件操作符
//                $this->checkConditionString($where_array);
//                $this->wheres[] = $where_array;
//            } else {
//                $this->wheres[] = [$field];
//            }
            if (is_array($value)) {
                foreach ($value as $item) {
                    $where_array = [$field, $condition, $item, $where_logic];
                    # 检测条件数组 下角标 必须为数字
                    $this->checkWhereArray($where_array, 0);
                    # 检测条件数组 检测第二个元素必须是限定的 条件操作符
                    $this->checkConditionString($where_array);
                    $this->wheres[] = $where_array;
                }
            } else {
                $where_array = [$field, $condition, $value, $where_logic];
                # 检测条件数组 下角标 必须为数字
                $this->checkWhereArray($where_array, 0);
                # 检测条件数组 检测第二个元素必须是限定的 条件操作符
                $this->checkConditionString($where_array);
                $this->wheres[] = $where_array;
            }
        }
        return $this;
    }

    public function limit($size, $offset = 0): QueryInterface
    {
        $this->limit = " LIMIT $offset,$size";
        return $this;
    }

    public function page(int $page = 1, int $pageSize = 20): QueryInterface
    {
        $offset = 0;
        if (1 < $page) {
            $offset = $pageSize * ($page - 1) /*+ 1*/
            ;
        }
        $this->limit              = " LIMIT $offset,$pageSize";
        $this->pagination['page'] = $page;
        return $this;
    }

    public function pagination(int $page = 1, int $pageSize = 20, array $params = []): QueryInterface
    {
        $this->pagination['page']     = $page;
        $this->pagination['pageSize'] = $pageSize;
        if ($params) {
            $this->pagination = array_merge($this->pagination, $params);
        }
        $this->pagination['params'] = $params;
        $this->page(intval($this->pagination['page']), $pageSize);
        $query                         = clone $this;
        $total                         = $query->total();
        $this->pagination['totalSize'] = $total;
        $lastPage                      = intval($total / $pageSize);
        if ($total % $pageSize) {
            $lastPage += 1;
        }
        $this->pagination['lastPage'] = $lastPage;
        return $this;
    }

    public function order(string $field, string $sort = 'DESC'): QueryInterface
    {
        if (!is_int(strpos($field, '`'))) {
            $field = $this->parserFiled($field);
        }
        $this->order[$field] = $sort;
        return $this;
    }

    public function group(string $fields): QueryInterface
    {
        $this->group_by = 'group by '.$fields;
        return $this;
    }

    public function having(string $having): QueryInterface
    {
        $this->having = 'having '.$having;
        return $this;
    }

    public function find(): QueryInterface
    {
        $this->limit(1, 0);
        $this->fetch_type = __FUNCTION__;
        $this->prepareSql(__FUNCTION__);
        return $this;
    }

    public function total(string $field = '*', string $alias = 'total'): int
    {
        $this->limit(1, 0);
        $this->fetch_type = 'find';
        $this->fields     = "count({$field}) as `{$alias}`";
        $this->prepareSql('find');
//        p($this->getLastSql());
        $result = $this->fetch();
        if (isset($result[$alias])) {
            $result = $result[$alias];
        }
        return intval($result);
    }

    public function select(): QueryInterface
    {
        $this->fetch_type = __FUNCTION__;
        $this->prepareSql(__FUNCTION__);
        return $this;
    }

    public function delete(): QueryInterface
    {
        $this->fetch_type = __FUNCTION__;
        $this->prepareSql(__FUNCTION__);
        return $this;
    }

    public function query(string $sql): QueryInterface
    {
        $this->sql          = $sql;
        $this->fetch_type   = __FUNCTION__;
        $this->PDOStatement = $this->connection->query($sql);
        return $this;
    }

    public function additional(string $additional_sql): QueryInterface
    {
        $this->additional_sql = $additional_sql;
        return $this;
    }

    public function fetch(string $model_class = ''): mixed
    {
        $result = $this->PDOStatement->execute($this->bound_values);

        $origin_data = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        $data        = [];
        if ($model_class) {
            foreach ($origin_data as $origin_datum) {
                $data[] = ObjectManager::make($model_class, ['data' => $origin_datum], '__construct');
            }
//            /** @var AbstractModel $model */
//            $model = ObjectManager::make($model_class, ['data' => end($data)->getData()], '__construct');
//            $data = $model->setFetchData($data);
        } else {
            $data = $origin_data;
        }
        switch ($this->fetch_type) {
            case 'find':
                $result = array_shift($data);
                if ($model_class && empty($result)) {
                    $result = ObjectManager::make($model_class, ['data' => []], '__construct');
                }
                break;
            case 'insert':
                $result = $this->connection->getLink()->lastInsertId();
                break;
            case 'query':
            case 'select':
                $result = $data;
                break;
            case 'delete':
            case 'update':
            default:
                break;
        }
        $this->fetch_type = '';
//        $this->clear();
        $this->clearQuery();
//        $this->reset();
        return $result;
    }

    public function fetchOrigin(): array
    {
        $this->PDOStatement->execute($this->bound_values);
        $origin_data = $this->PDOStatement->fetchAll(PDO::FETCH_ASSOC);
        $this->clearQuery();
        return $origin_data;
    }


    public function clear(string $type = ''): QueryInterface
    {
        if ($type) {
            $attr_var_name = $type;
            if (DEV && !isset(self::init_vars[$attr_var_name])) {
                $this->exceptionHandle(__('不支持的清理类型：%1 支持的初始化类型：%2', [$attr_var_name, var_export(self::init_vars, true)]));
            }
            $this->$attr_var_name = self::init_vars[$attr_var_name];
        } else {
            $this->reset();
        }
        return $this;
    }


    public function clearQuery(string $type = ''): QueryInterface
    {
        if ($type) {
            $attr_var_name = $type;
            if (DEV && !isset(self::init_vars[$attr_var_name])) {
                $this->exceptionHandle(__('不支持的清理类型：%1 支持的初始化类型：%2', [$attr_var_name, var_export(self::init_vars, true)]));
            }
            $this->$attr_var_name = self::init_vars[$attr_var_name];
        } else {
            foreach (self::query_vars as $query_field => $query_var) {
                $this->$query_field = $query_var;
            }
        }
        return $this;
    }

    public function reset(): QueryInterface
    {
        foreach (self::init_vars as $init_field => $init_var) {
            $this->$init_field = $init_var;
        }
        $this->PDOStatement = null;
        return $this;
    }

    public function beginTransaction(): void
    {
        $this->connection->getLink()->beginTransaction();
    }

    public function rollBack(): void
    {
        $this->connection->getLink()->rollBack();
    }

    public function commit(): void
    {
        $this->connection->getLink()->commit();
    }

    /**
     * 归档数据
     *
     * @param string $period ['all'=>'全部','today'=>'今天','yesterday'=>'昨天','current_week'=>'这周','near_week'=>'最近一周','last_week'=>'上周','near_month'=>'近三十天','current_month'=>'本月','last_month'=>'上一月','quarter'=>'本季度','last_quarter'=>'上个季度','current_year'=>'今年','last_year'=>'上一年']
     * @param string $field
     *
     * @return $this
     */
    public function period(string $period, string $field = 'main_table.create_time'): static
    {
        switch ($period) {
            case 'all':
                break;
            case 'today':
                #今天
                $this->where("TO_DAYS({$field})=TO_DAYS(NOW())");
                break;
            case 'yesterday':
                #昨天
                $this->where("DATE({$field}) = DATE(CURDATE()-1)");
                break;
            case 'current_week':
                #查询当前这周的数据
                $this->where("YEARWEEK(DATE_FORMAT({$field},'%Y-%m-%d')) = YEARWEEK(NOW())");
                break;
            case 'near_week':
                #近7天
                $this->where("DATE_SUB(CURDATE(), INTERVAL 7 DAY) <= DATE({$field})");
                break;
            case 'last_week':
                #查询上周的数据
                $this->where("YEARWEEK(DATE_FORMAT({$field},'%Y-%m-%d')) =YEARWEEK(NOW())-1");
                break;
            case 'near_month':
                #近30天
                $this->where("DATE_SUB(CURDATE(), INTERVAL 30 DAY) <= DATE({$field})");
                break;
            case 'current_month':
                # 本月
                $this->where("DATE_FORMAT({$field},'%Y%m') =DATE_FORMAT(CURDATE(),'%Y%m')");
                break;
            case 'last_month':
                #上一月
                $this->where("PERIOD_DIFF(DATE_FORMAT( NOW(),'%Y%m'),DATE_FORMAT({$field},'%Y%m')) =1");
                break;
            case 'quarter':
                #查询本季度数据
                $this->where("QUARTER({$field})=QUARTER(NOW())");
                break;
            case 'last_quarter':
                #查询上季度数据
                $this->where("QUARTER({$field})=QUARTER(DATE_SUB(NOW(),INTERVAL 1 QUARTER))");
                break;
            case 'current_year':
                #查询本年数据
                $this->where("YEAR({$field})=YEAR(NOW())");
                break;
            case 'last_year':
                #查询上年数据
                $this->where("YEAR({$field})=YEAR(DATE_SUB(NOW(),INTERVAL 1 YEAR))");
                break;
            default:
        }
        return $this;
    }

    public function getLastSql(bool $format = true): string
    {
        foreach ($this->bound_values as $where_key => $wheres_value) {
            $wheres_value = "'{$wheres_value}'";
            $this->sql    = str_replace($where_key, $wheres_value, $this->sql);
        }
        if ($format) {
            return \SqlFormatter::format($this->sql);
        }
        return $this->sql;
    }

    public function getPrepareSql(bool $format = true): string
    {
        if ($format) {
            return \SqlFormatter::format($this->sql);
        }
        return $this->sql;
    }
}
