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
                throw new DbException(__('Where查询异常：%1,%2,%3', ["第{$f_key}个条件数组错误", '出错的数组：["' . implode('","', $where_array) . '"]', "示例：where([['name','like','%张三%','or'],['name','like','%李四%']])"]));
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
            throw new DbException(__('当前错误的条件操作符：%1 ,当前的条件数组：%2, 允许的条件符：%3', [$where_array[1], '["' . implode('","', $where_array) . '"]', '["' . implode('","', $conditions) . '"]']));
        }
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
        if ($this->_table == '') {
            throw new DbException(__('没有指定table表名！'));
        }
        # 处理 joins
        $joins = '';
        foreach ($this->_joins as $join) {
            $joins .= " {$join[2]} JOIN {$join[0]} ON {$join[1]} ";
        }
        # 处理 Where 条件
        $wheres = '';
        $wheres_values = [];
        if ($this->_wheres) {
            $wheres .= ' WHERE ';
            $logic = 'AND ';
            foreach ($this->_wheres as $key => $where) {
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
                        $where[0] = '`'.str_replace('.', '`.`', $where[0]).'`';
                        # 处理别名
                        $param = str_replace('.', '__', $param) . $key;
                        $wheres_values[$param] = $where[2];
                        $where[2] = $param;
                        $wheres .= '(' . implode(' ', $where) . ') ' . $logic;
                }

            }
            $wheres = rtrim($wheres, $logic);
        }
        $sql = '';
        switch ($action) {
            case 'select':
                $sql = "SELECT {$this->_fields} FROM {$this->_table} {$this->_table_alias} {$joins} {$wheres} {$this->_limit}";
                break;
            case 'delete':
                $sql = "DELETE FROM {$this->_table} {$wheres}";
                break;
            case 'update':
                $update = '';
                foreach ($this->_updates as $update) {
                    
                }
                $sql = "UPDATE {$this->_table} SET `{$this->_update}` {$wheres}";
                break;
            case 'find':
            default:
                $sql = "SELECT {$this->_fields} FROM {$this->_table} {$this->_table_alias} {$joins} {$wheres} LIMIT 1";
        }
        pp($sql);
        $linker = $this->linker->getLink()->prepare($sql);
        $linker->execute($wheres_values);
        return [$linker, $wheres_values, $sql];
    }
}