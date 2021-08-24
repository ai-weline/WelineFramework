<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Linker\Query;


use Weline\Framework\Database\LinkerFactory;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Database\Cache\DbCache;
use Weline\Framework\Database\Exception\DbException;
use Weline\Framework\Exception\Core;

trait QueryTrait
{
    private LinkerFactory $linker;
    private CacheInterface $cache;
    private string $db_name;

    function __construct(
        LinkerFactory $linker,
        DbCache $cache
    )
    {
        $this->linker = $linker;
        $this->db_name = $linker->getConfigProvider()->getDatabase();
        $this->cache = $cache->create();
    }

    function __sleep()
    {
        return array('cache', 'db_name', 'linker');
    }

    function getTable($table_name): string
    {
        return "`{$this->db_name}`.`{$table_name}`";
    }

    /**
     * @DESC          | 获取链接
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 21:10
     *
     * @return LinkerFactory
     */
    public function getLinker(): LinkerFactory
    {
        return $this->linker;
    }

    /**
     * @DESC          | 设置链接
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 21:10
     *
     * @param LinkerFactory $linker
     */
    public function setLinker(LinkerFactory $linker): void
    {
        $this->linker = $linker;
    }


    /**
     * @DESC          |  # 检测条件数组 下角标 必须为数字
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 22:39
     * 参数区：
     * @param array $where_array
     * @param mixed $f_key
     * @throws DbException
     * @throws \Weline\Framework\App\Exception
     */
    private function checkWhereArray(array $where_array, mixed $f_key)
    {

        foreach ($where_array as $f_item_key => $f_item_value) {
            if (!is_numeric($f_item_key)) {
                $this->$this->exceptionHandle(__('Where查询异常：%1,%2,%3', ["第{$f_key}个条件数组错误", '出错的数组：["' . implode('","', $where_array) . '"]', "示例：where([['name','like','%张三%','or'],['name','like','%李四%']])"]));
            }
        }
    }

    /**
     * @DESC          | 检测条件参数是否正确
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 22:30
     * 参数区：
     * @param array $where_array
     * @return string
     * @throws DbException
     */
    private function checkConditionString(array $where_array): string
    {
        $conditions = [
            '>',
            '>=',
            '<',
            '=<',
            '<>',
            'like',
            '=',
        ];
        if (in_array($where_array[1], $conditions)) {
            return $where_array[1];
        } else {
            $this->exceptionHandle(__('当前错误的条件操作符：%1 ,当前的条件数组：%2, 允许的条件符：%3', [$where_array[1], '["' . implode('","', $where_array) . '"]', '["' . implode('","', $conditions) . '"]']));
        }
        return '';
    }

    /**
     * @DESC          # 准备sql
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/17 22:52
     * 参数区：
     */
    private function prepareSql($action)
    {
        if ($this->table == '') $this->exceptionHandle(__('没有指定table表名！'));
        # 处理 joins
        $joins = '';
        foreach ($this->joins as $join) {
            $joins .= " {$join[2]} JOIN {$join[0]} ON {$join[1]} ";
        }
        # 处理 Where 条件
        $wheres = '';
        if ($this->wheres) {
            $wheres .= ' WHERE ';
            $logic = 'AND ';
            foreach ($this->wheres as $key => $where) {
                # 如果自己设置了where 逻辑连接符 就修改默认的连接符 AND
                if (isset($where[3])) {
                    $logic = array_pop($where) . ' ';
                }
                switch (count($where)) {
                    # 字段等于sql
                    case 1:
                        $wheres .= $where[0] . " {$logic} ";
                        break;
                    # 默认where逻辑连接符为AND
                    default:
                        $param = ':' . trim($where[0], '`');
                        $where[0] = '`' . str_replace('.', '`.`', $where[0]) . '`';
                        # 处理别名
                        $param = str_replace('.', '__', $param) . $key;
                        $this->wheres_values[$param] = $where[2];
                        $where[2] = $param;
                        $wheres .= '(' . implode(' ', $where) . ') ' . $logic;
                }

            }
            $wheres = rtrim($wheres, $logic);
        }
        # 排序
        $order = '';
        foreach ($this->order as $field => $dir) {
            $order .= "$field $dir";
        }
        if ($order) $order = 'ORDER BY ' . $order;

        # 匹配sql
        switch ($action) {
            case 'insert' :
                $values = '';
                foreach ($this->insert as $insert) {
                    $values .= '(' . implode(',', $insert) . '),';
                }
                $values = rtrim($values, ',');
                $sql = "INSERT INTO {$this->table} {$this->fields} VALUES {$values}";
                break;
            case 'select' :
                $sql = "SELECT {$this->fields} FROM {$this->table} {$this->table_alias} {$joins} {$wheres} {$order} {$this->additional_sql} {$this->limit}";
                break;
            case 'delete' :
                $sql = "DELETE FROM {$this->table} {$wheres} {$this->additional_sql}";
                break;
            case 'update' :
                $sql = "UPDATE {$this->table}  {$this->table_alias} SET {$this->updates} {$wheres} {$this->additional_sql} ";
                break;
            default :
                $sql = "SELECT {$this->fields} FROM {$this->table} {$this->table_alias} {$joins} {$wheres}  {$order} {$this->additional_sql} LIMIT 1";
                break;
        };
        # 预置sql
        $this->PDOStatement = $this->linker->getLink()->prepare($sql);
        $this->sql = $sql;
    }

    /**
     * @DESC          # 异常函数
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/23 21:28
     * 参数区：
     * @param $words
     * @throws DbException
     */
    protected function exceptionHandle($words)
    {
        throw new DbException($words);
    }
}