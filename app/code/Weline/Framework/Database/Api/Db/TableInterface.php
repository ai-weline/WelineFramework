<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Api\Db;

interface TableInterface
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
    const table_ALERT_FIELDS = 'alter_fields';
    const table_DELETE_FIELDS = 'delete_fields';
    const table_INDEXS = 'indexes';
    const table_FOREIGN_KEYS = 'foreign_keys';
    const table_TYPE = 'type';
    const table_CONSTRAINTS = 'constraints';
    const table_ADDITIONAL = 'additional';

    const init_vars = array(
        self::table_TABLE => '',
        self::table_COMMENT => '',
        self::table_FIELDS => array(),
        self::table_ALERT_FIELDS => array(),
        self::table_DELETE_FIELDS => array(),
        self::table_INDEXS => array(),
        self::table_FOREIGN_KEYS => array(),
        self::table_CONSTRAINTS => '',
        self::table_ADDITIONAL => 'ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;',
    );
    /**
     * @DESC          # 创建表
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 21:03
     * 参数区：
     * @return \Weline\Framework\Database\Api\Db\Ddl\Table\CreateInterface
     */
    function createTable(): \Weline\Framework\Database\Api\Db\Ddl\Table\CreateInterface;

    /**
     * @DESC          # 修改表
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 21:04
     * 参数区：
     * @return \Weline\Framework\Database\Api\Db\Ddl\Table\AlterInterface
     */
    function alterTable(): \Weline\Framework\Database\Api\Db\Ddl\Table\AlterInterface;
}