<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Db\Ddl;

use Weline\Framework\App\Exception;
use Weline\Framework\Database\DbManager;
use Weline\Framework\Database\LinkerFactory;
use Weline\Framework\Manager\ObjectManager;

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

    const table_TABLE = 'table';
    const table_COMMENT = 'comment';
    const table_FIELDS = 'fields';
    const table_INDEXS = 'indexes';
    const table_FOREIGN_KEYS = 'foreign_keys';
    const table_TYPE = 'type';
    const table_CONSTRAINTS = 'constraints';
    const table_ADDITIONAL = 'additional';

    const init_vars = array(
        self::table_TABLE => '',
        self::table_COMMENT => '',
        self::table_FIELDS => array(),
        self::table_INDEXS => array(),
        self::table_FOREIGN_KEYS => array(),
        self::table_CONSTRAINTS => '',
        self::table_ADDITIONAL => 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;',
    );

    // 数据字段
    private string $table;

    private string $comment;

    private array $fields = array();
    private array $indexes = array();

    private array $foreign_keys = array();

    private string $constraints = '';

    private string $additional = 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;';

    private LinkerFactory $linker;

    /**
     * Table constructor.
     * @param DbManager $dbManager
     * @throws Exception
     * @throws \ReflectionException
     * @throws \Weline\Framework\Database\Exception\LinkException
     */
    public function __construct(DbManager $dbManager)
    {
        $this->linker = $dbManager->create();
    }

    /**
     * @DESC          # 创建表
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 21:22
     * 参数区：
     * @param string $table
     * @param string $comment
     * @return $this
     */
    public function createTable(string $table, string $comment = ''): static
    {
        # 清空所有表属性 重新创建表
        foreach (self::init_vars as $attr => $init_var) {
            $this->$attr = $init_var;
        }
        # 重新赋予新表的值
        $this->table = $table;
        $this->comment = $comment ? "COMMENT '{$comment}'" : '';
        return $this;
    }

    /**
     * @DESC          # 数据库链接
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:43
     * 参数区：
     * @return LinkerFactory
     */
    function getLinker(): LinkerFactory
    {
        return $this->linker;
    }

    /**
     * @DESC          # 添加字段
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/26 21:31
     * 参数区：
     * @param string $field_name 字段名
     * @param string $type 字段类型
     * @param int|null $length 长度
     * @param string $options 配置
     * @param string $comment 字段注释
     * @return Table
     */
    public function addColumn(string $field_name, string $type, ?int $length, string $options, string $comment): Table
    {
        $type_length = $length ? "{$type}({$length})" : $type;
        $this->fields[] = "`{$field_name}` {$type_length} {$options} COMMENT '{$comment}'," . PHP_EOL;

        return $this;
    }

    /**
     * @DESC         |添加索引
     *
     * 参数区：
     *
     * @param string $type
     * @param string $name
     * @param array|string $column
     * @return Table
     */
    public function addIndex(string $type, string $name, array|string $column): Table
    {
        switch ($type) {
            case self::index_type_DEFAULT:
                $this->indexes[] = "INDEX {$name}(`{$column}`)," . PHP_EOL;

                break;
            case self::index_type_FULLTEXT:
                $this->indexes[] = "FULLTEXT INDEX {$name}(`{$column}`)," . PHP_EOL;

                break;
            case self::index_type_UNIQUE:
                $this->indexes[] = "UNIQUE INDEX {$name}(`{$column}`)," . PHP_EOL;

                break;
            case self::index_type_SPATIAL:
                $this->indexes[] = "SPATIAL INDEX {$name}(`{$column}`)," . PHP_EOL;

                break;
            case self::index_type_KEY:
                $this->indexes[] = "KEY IDX {$name}(`{$column}`)," . PHP_EOL;

                break;
            case self::index_type_MULTI:
                $type_of_column = getType($column);
                if (!is_array($column)) {
                    new Exception(self::index_type_MULTI . __('：此索引的column需要array类型,当前类型') . "{$type_of_column}" . ' 例如：[ID,NAME(19),AGE]');
                }
                $column = implode(',', $column);
                $this->indexes[] = "INDEX {$name}(`$column`)," . PHP_EOL;

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
    public function addAdditional(string $additional_sql = 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;'): Table
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
     * @DESC          # 建表
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:36
     * 参数区：
     * @return \Weline\Framework\Database\Linker\QueryInterface
     */
    public function create(): \Weline\Framework\Database\Linker\QueryInterface
    {
        // 字段
        $fields_str = '';
        foreach ($this->fields as $field) {
            if (end($this->fields) === $field) {
                $field = trim($field, PHP_EOL);
                if (empty($this->indexes)) {
                    $field = trim($field, ',');
                }// 如果没有设置索引
            }
            $fields_str .= $field;
        }
        $fields_str = trim($fields_str, ',');
        if (!strstr($fields_str, '`create_time`')) {
            $fields_str .= ',' . PHP_EOL;
            $create_time_comment_words = __('创建时间');
            $fields_str .= "`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '{$create_time_comment_words}'," . PHP_EOL;
        }
        if (!strstr($fields_str, '`update_time`')) {
            if (strstr($fields_str, ',' . PHP_EOL)) {
                $fields_str = rtrim($fields_str, ',' . PHP_EOL);
            }
            $fields_str = rtrim($fields_str, ',');
            $fields_str .= ',' . PHP_EOL;
            $update_time_comment_words = __('更新时间');
            $fields_str .= "`update_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '{$update_time_comment_words}'," . PHP_EOL;
        }
        if (strstr($fields_str, ',' . PHP_EOL)) {
            $fields_str = rtrim($fields_str, ',' . PHP_EOL);
        }
        // 索引
        $indexes_str = '';
        foreach ($this->indexes as $index) {
            if (end($this->indexes) === $index) {
                if (empty($this->constraints)) {
                    $index = trim(trim($index, PHP_EOL), ',');
                }
            }
            $indexes_str .= $index;
        }
        if ($indexes_str) {
            $fields_str .= ',';
        }
        $indexes_str = rtrim($indexes_str, PHP_EOL);
        $indexes_str = rtrim($indexes_str, '\n\r');
        // 外键
        $foreign_key_str = '';
        foreach ($this->foreign_keys as $foreign_key) {
            if (end($this->foreign_keys) === $foreign_key) {
                if (empty($this->constraints)) {
                    $foreign_key = trim(trim($foreign_key, PHP_EOL), ',');
                }
            }
            $foreign_key_str .= $foreign_key;
        }
        if ($foreign_key_str) {
            $indexes_str .= ',';
        }
        $foreign_key_str = rtrim($foreign_key_str, PHP_EOL);
        $foreign_key_str = rtrim($foreign_key_str, '\n\r');

        $sql = <<<createSQL
CREATE TABLE {$this->table}(
 {$fields_str}
 {$indexes_str}
 {$foreign_key_str}
 {$this->constraints}
){$this->comment} {$this->additional}
createSQL;
        return $this->linker->query($sql);
    }

    /**
     * @DESC          # 修改表
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:33
     * 参数区：
     * @param string $sql
     * @return \Weline\Framework\Database\Linker\QueryInterface
     */
    public function alert(string $sql): \Weline\Framework\Database\Linker\QueryInterface
    {
        return $this->linker->query($sql);
    }

    /**
     * @DESC          # 查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:34
     * 参数区：
     * @param string $sql
     * @return \Weline\Framework\Database\Linker\QueryInterface
     */
    public function query(string $sql): \Weline\Framework\Database\Linker\QueryInterface
    {
        return $this->linker->query($sql);
    }

    /**
     * @DESC          # 数据库类型
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:36
     * 参数区：
     * @return string
     */
    public function getType(): string
    {
        return $this->linker->getConfigProvider()->getDbType();
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->linker->getConfigProvider()->getPrefix();
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @DESC          # 添加外键
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/31 20:37
     * 参数区：
     * @param string $FK_Name
     * @param string $FK_Field
     * @param string $references_table
     * @param string $references_field
     * @param false $on_delete
     * @param false $on_update
     * @return $this
     */
    function addForeignKey(string $FK_Name, string $FK_Field, string $references_table, string $references_field, bool $on_delete = false, bool $on_update = false): static
    {
        $on_delete_str = $on_delete ? 'on delete cascade' : '';
        $on_update_str = $on_update ? 'on update cascade' : '';
        $this->foreign_keys[] = "constraint {$FK_Name} foreign key ({$FK_Field}) references {$references_table}({$references_field}) {$on_delete_str} {$on_update_str}";
        return $this;
    }
}
