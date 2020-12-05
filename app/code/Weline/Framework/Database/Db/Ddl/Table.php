<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Db\Ddl;

use Weline\Framework\App\Exception;
use Weline\Framework\Database\DbManager;

class Table
{
    /**
     * 字段类型
     */
    const column_type_BOOLEAN = 'boolean';

    const column_type_VARCHAR = 'varchar';

    const column_type_SMALLINT = 'smallint';

    const column_type_INTEGER = 'integer';

    const column_type_BIGINT = 'bigint';

    const column_type_FLOAT = 'float';

    const column_type_NUMERIC = 'numeric';

    const column_type_DECIMAL = 'decimal';

    const column_type_DATE = 'date';

    const column_type_TIMESTAMP = 'timestamp';

    // 能够支持从1970年开始的日期时间+在一些关系数据库中的自动触发器
    const column_type_DATETIME = 'datetime';

    // 能够支持1970年以前的长时间数据
    const column_type_TEXT = 'text';

    // 一个真正的blob，以二进制形式存储在DB中
    const column_type_BLOB = 'blob';

    // 当查询参数不能使用语句选项时，用于向后兼容
    const column_type_VARBINARY = 'varbinary';

    /**
     * 索引类型
     */
    const index_type_DEFAULT = 'DEFAULT';

    const index_type_FULLTEXT = 'FULLTEXT';//-- FullText 全文索引，需指定存储引擎为MyISAM，MySQL默认存储引擎为InnoDB

    const index_type_SPATIAL = 'SPATIAL';//-- SPATIAL 创建空间索引，需指定存储引擎为MyISAM，MySQL默认存储引擎为InnoDB

    const index_type_UNIQUE = 'UNIQUE';//-- 创建唯一索引

    const index_type_MULTI = 'MULTI';//-- 创建组合索引

    const index_type_KEY = 'KEY';//--用KEY创建普通索引

    // 数据字段
    private array $_fields;

    private array $_indexes;

    private string $type;

    private string $table;

    private string $comment;

    private string $constraints = '';

    private string $additional = 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;';

    private DbManager $db;

    /**
     * Table 初始函数...
     * @param string $table
     * @param string $comment
     */
    public function __construct(string $table, string $comment = '')
    {
        $this->db      = new DbManager();
        $db            = $this->db->getConfig();
        $this->type    = $db['default'];
        $this->table   = $table;
        $this->comment = $comment ? "COMMENT '{$comment}'" : '';
    }

    /**
     * @DESC         |添加字段
     *
     * 参数区：
     * @param string $field_name
     * @param string $type
     * @param string|null $length
     * @param string $options
     * @param string $comment
     */
    public function addColumn(string $field_name, string $type, string $length, string $options, string $comment): Table
    {
        $type_length     = $length ? "{$type}({$length})" : $type;
        $this->_fields[] = "`{$field_name}` {$type_length} {$options} COMMENT '{$comment}'," . PHP_EOL;

        return $this;
    }

    /**
     * @DESC         |添加索引
     *
     * 参数区：
     *
     * @param string $type
     * @param string $name
     * @param string|array $column
     * @return Table
     */
    public function addIndex(string $type, string $name, $column): Table
    {
        switch ($type) {
            case self::index_type_DEFAULT:
                $this->_indexes[] = "INDEX {$name}({$column})," . PHP_EOL;

                break;
            case self::index_type_FULLTEXT:
                $this->_indexes[] = "FULLTEXT INDEX {$name}({$column})," . PHP_EOL;

                break;
            case self::index_type_UNIQUE:
                $this->_indexes[] = "UNIQUE INDEX {$name}({$column})," . PHP_EOL;

                break;
            case self::index_type_SPATIAL:
                $this->_indexes[] = "SPATIAL INDEX {$name}({$column})," . PHP_EOL;

                break;
            case self::index_type_KEY:
                $this->_indexes[] = "KEY IDX {$name}({$column})," . PHP_EOL;

                break;
            case self::index_type_MULTI:
                $type_of_column = getType($column);
                if (! is_array($column)) {
                    new Exception(self::index_type_MULTI . __('：此索引的column需要array类型,当前类型') . "{$type_of_column}" . ' 例如：[ID,NAME(19),AGE]');
                }
                $column           = implode(',', $column);
                $this->_indexes[] = "INDEX {$name}($column)," . PHP_EOL;

                break;
            default:
                new Exception(__('未知的索引类型：') . $type);
        }

        return $this;
    }

    /**
     * @DESC         |建表附加
     *
     * 参数区：
     *
     * @param string $additional_sql
     * @return $this
     */
    public function addAdditional($additional_sql = 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;'): Table
    {
        $this->additional = $additional_sql;

        return $this;
    }

    /**
     * @DESC         |表约束
     *
     * 参数区：
     * @param string $constraints
     * @return Table
     */
    public function addConstraints(string $constraints = ''): Table
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * @DESC         |建表
     *
     * 参数区：
     */
    public function create()
    {
        // 字段
        $fields_str = '';
        foreach ($this->_fields as $field) {
            if (end($this->_fields) === $field) {
                $field = trim($field, PHP_EOL);
                if (empty($this->_indexes)) {
                    $field = trim($field, ',');
                }// 如果没有设置索引
            }
            $fields_str .= $field;
        }
        // 索引
        $indexes_str = '';
        foreach ($this->_indexes as $index) {
            if (end($this->_indexes) === $index) {
                if (empty($this->constraints)) {
                    $index = trim(trim($index, PHP_EOL), ',');
                }
            }
            $indexes_str .= $index;
        }
        $sql = <<<createSQL
CREATE TABLE {$this->table}(
 {$fields_str}
 {$indexes_str}
 {$this->constraints}
){$this->comment} {$this->additional}
createSQL;
//        if (DEV) p($sql, 1);
        return $this->db->query($sql);
    }

    /**
     * @DESC         |修改表
     *
     * 参数区：
     * @param string $sql
     * @return mixed
     */
    public function alert(string $sql)
    {
        return $this->db->query($sql);
    }

    /**
     * @DESC         |查询
     *
     * 参数区：
     *
     * @param string $sql
     * @return mixed
     */
    public function query(string $sql)
    {
        return $this->db->query($sql);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }
}
