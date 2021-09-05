<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Db\Ddl\Table;

use Weline\Framework\App\Exception;
use Weline\Framework\Database\Api\Db\Ddl\Table\AlterInterface;
use Weline\Framework\Database\Db\Ddl\TableAbstract;
use Weline\Framework\Database\Exception\DbException;

class Alter extends TableAbstract implements AlterInterface
{

    public function forTable(string $table, string $comment = ''): AlterInterface
    {
        # 开始表操作
        $this->startTable($table, $comment);
        return $this;
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
     * @return AlterInterface
     */
    public function addColumn(string $field_name, string $type, ?int $length, string $options, string $comment): AlterInterface
    {
        $type_length = $length ? "{$type}({$length})" : $type;
        $this->fields[] = "`{$field_name}` {$type_length} {$options} COMMENT '{$comment}'," . PHP_EOL;

        return $this;
    }

    /**
     * @DESC          # 删除字段
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 16:09
     * 参数区：
     * @param string $field_name
     * @return AlterInterface
     */
    public function deleteColumn(string $field_name): AlterInterface
    {
        $this->delete_fields[$field_name] = $field_name;
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
     * @return AlterInterface
     */
    public function addIndex(string $type, string $name, array|string $column): AlterInterface
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
     * @return AlterInterface
     */
    public function addAdditional(string $additional_sql = 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;'): AlterInterface
    {
        $this->additional = $additional_sql;

        return $this;
    }

    /**
     * @DESC         |表约束
     *
     * 参数区：
     * @param string $constraints
     * @return AlterInterface
     */
    public function addConstraints(string $constraints = ''): AlterInterface
    {
        $this->constraints = $constraints;

        return $this;
    }

    public function alterColumn(string $old_field, string $field_name, string $after_field = '', string $type = null, ?int $length = null, string $options = null, string $comment = null): AlterInterface
    {
        $type_length = $length ? "{$type}({$length})" : $type;
        $this->alter_fields[$old_field] = ['field_name' => $field_name, 'after_field' => $after_field, 'type_length' => $type_length, 'options' => $options, 'comment' => $comment,];

        return $this;
    }

    public function alter(): bool
    {
//        try {
        # 检测更新表注释 TODO 持续完成ORM修改表
        $ddl = $this->getCreateTableSql();
        $ddl_comment_array = explode('COMMENT=', $ddl);
        $comment = str_replace('"', '', array_pop($ddl_comment_array));
        $comment = str_replace('\'', '', $comment);
        # --检测存在评论，并且评论不相同时，更新表评论
        if ($this->comment && $comment !== $this->comment) {
            $this->query->beginTransaction();
            try {
                $this->query->query("ALTER TABLE {$this->table} COMMENT='{$this->comment}'")->fetch();
                $this->query->commit();
            } catch (\Exception $exception) {
                $this->query->rollBack();
                throw new DbException(__('更新表注释错误：%1', $exception->getMessage()));
            }

        }
        $table_fields = $this->getTableColumns();
        foreach ($table_fields as $table_field) {
            # --如果存在则修改
            if (isset($this->alter_fields[$table_field['Field']]) && $alter_field = $this->alter_fields[$table_field['Field']]) {
                if ($table_field['Field'] !== $alter_field['field_name']) {
                    # --与数据库中的字段类型 比较
                    $type_length = $table_field['Type'];
                    if ($alter_field['type_length'] && ($table_field['Type'] === $alter_field['type_length'])) {
                        $type_length = $alter_field['type_length'];
                    }
                    # --与数据库中的字段评论 比较
                    $comment = $table_field['Comment'];
                    if ($alter_field['comment'] && ($table_field['Comment'] === $alter_field['comment'])) {
                        $comment = $alter_field['comment'];
                    }
                    # --与数据库中的字段其他参数 比较
                    p($table_field, true);
                    $options = '';
                    if ($alter_options = $alter_field['options']) {
                        $options = $alter_options;
                    } else {
                        # --是否允许空
                        if ('YES' === $table_field['Null']) {
                            $options .= ' IS NULL ';
                        } else {
                            $options .= ' NOTE NULL ';
                        }
                        # --默认值
                        if ($table_field['Default']) {
                            $options .= " DEFAULT '{$table_field['Default']}' ";
                        }
                        # --列索引键
                        if ($key = $table_field['Key']) {
                            $options .= match ($key) {
                                'PRI' => ' PRIMARY KEY ',
                                'UNI' => ' UNIQUE ',
                                'MUL' => ' ',
                            };
                        }
                    }
                    $sql = "ALERT TABLE {$this->table} CHANGE `{$table_field['Field']}` `{$alter_field['field_name']}` {$type_length} {$options} comment '{$comment}'";
                    p($sql);
                }

                p("ALERT TABLE {$this->table} {$action} column `{$alter_field['field_name']}` {$alter_field['type_length']} comment '主键ID'");

                $this->query("ALERT TABLE {$this->table} modify column `{$alter_field['field_name']}` {$alter_field['type_length']} comment '主键ID'")->fetch();
            }
        }


        return true;
    }

    function addForeignKey(string $FK_Name, string $FK_Field, string $references_table, string $references_field, bool $on_delete = false, bool $on_update = false): AlterInterface
    {
        $on_delete_str = $on_delete ? 'on delete cascade' : '';
        $on_update_str = $on_update ? 'on update cascade' : '';
        $this->foreign_keys[] = "constraint {$FK_Name} foreign key ({$FK_Field}) references {$references_table}({$references_field}) {$on_delete_str} {$on_update_str}";
        return $this;
    }
}