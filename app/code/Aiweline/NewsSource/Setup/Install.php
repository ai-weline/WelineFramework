<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\NewsSource\Setup;

use Weline\Framework\Database\Db\Ddl\Table;
use Weline\Framework\Setup\Data;
use Weline\Framework\Setup\InstallInterface;

class Install implements InstallInterface
{
    const table_NEWS_SOURCE = 'aiweline_news_source';

    const table_NEWS_CATEGORY = 'aiweline_news_category';

    const table_NEWS = 'aiweline_news';

    const table_NEWS_POST = 'aiweline_news_post';

    const table_NEWS_USER = 'aiweline_news_user';

    public function setup(Data\Setup $setup, Data\Context $context): void
    {
        $printer = $setup->getPrinter();
        $printer->note('安装开始...');
        $db = $setup->getDb();
        /**新闻分类表*/
        $printer->warning('安装数据库表：' . self::table_NEWS_CATEGORY);
        if (! $db->tableExist(self::table_NEWS_CATEGORY)) {
            $setup->getDb()->createTable(
                self::table_NEWS_CATEGORY,
                '新闻分类表'
            )->addColumn(
                'id',
                Table::column_type_INTEGER,
                11,
                'primary key NOT NULL AUTO_INCREMENT',
                'ID'
            )->addColumn(
                'name',
                Table::column_type_VARCHAR,
                '60',
                'NOT NULL',
                '分类名'
            )->addColumn(
                'parent_id',
                Table::column_type_INTEGER,
                11,
                '',
                '父分类'
            )->addIndex(
                Table::index_type_FULLTEXT,
                'name',
                'name'
            )->addIndex(
                Table::index_type_DEFAULT,
                'parent_id',
                'parent_id'
            )->create();
        }

        /**新闻来源表*/
        $printer->warning('安装数据库表：' . self::table_NEWS_SOURCE);
        if (! $db->tableExist(self::table_NEWS_SOURCE)) {
            $db->createTable(
                self::table_NEWS_SOURCE,
                '新闻来源表'
            )->addColumn(
                'id',
                Table::column_type_INTEGER,
                11,
                'primary key NOT NULL AUTO_INCREMENT',
                'ID'
            )->addColumn(
                'name',
                Table::column_type_VARCHAR,
                '60',
                'NOT NULL',
                '来源名称'
            )->addColumn(
                'info',
                Table::column_type_TEXT,
                1000,
                '',
                '信息'
            )->addColumn(
                'create_time',
                Table::column_type_DATETIME,
                '',
                'NOT NULL DEFAULT CURRENT_TIMESTAMP',
                '建立时间'
            )->addIndex(
                Table::index_type_FULLTEXT,
                'name',
                'name'
            )->addConstraints(
                'UNIQUE KEY(name)'
            )->create();
        }

        /**新闻表*/
        $printer->warning('安装数据库表：' . self::table_NEWS);
        if (! $db->tableExist(self::table_NEWS)) {
            $foreign_table = $db->getTable(self::table_NEWS_CATEGORY);
            $setup->getDb()->createTable(
                self::table_NEWS,
                '新闻表'
            )->addColumn(
                'id',
                Table::column_type_INTEGER,
                11,
                'primary key NOT NULL AUTO_INCREMENT',
                'ID'
            )->addColumn(
                'source_id',
                Table::column_type_INTEGER,
                11,
                'NOT NULL',
                '资源ID'
            )->addColumn(
                'category_id',
                Table::column_type_INTEGER,
                11,
                'NOT NULL',
                '分类ID'
            )->addColumn(
                'title',
                Table::column_type_VARCHAR,
                '60',
                'NOT NULL',
                '新闻标题'
            )->addColumn(
                'author',
                Table::column_type_VARCHAR,
                '20',
                'NOT NULL',
                '作者'
            )->addColumn(
                'abstract',
                Table::column_type_VARCHAR,
                '120',
                'NOT NULL',
                '新闻摘要'
            )->addColumn(
                'pushtime',
                Table::column_type_TIMESTAMP,
                '',
                '',
                '发布时间'
            )->addColumn(
                'create_time',
                Table::column_type_DATETIME,
                '',
                'NOT NULL DEFAULT CURRENT_TIMESTAMP',
                '创建时间'
            )->addIndex(
                Table::index_type_DEFAULT,
                'category_id',
                'category_id'
            )->addIndex(
                Table::index_type_FULLTEXT,
                'title',
                'title'
            )->addConstraints(
                "foreign key (category_id) references {$foreign_table}(id) on delete cascade on update cascade"
            )
                /*外键不支持分区使用，只能舍去分区*/
                /*->addAdditional(
                "partition by range(create_time)(
        partition p202007 values less than (TO_DAYS('20200701')),
        partition p202008 values less than (TO_DAYS('20200801')),
        partition p202009 values less than (TO_DAYS('20200901')),
        partition p202010 values less than (TO_DAYS('20201001')),
        partition p202011 values less than (TO_DAYS('20201101')),
        partition p202012 values less than (TO_DAYS('20201201')),
        partition p202101 values less than (TO_DAYS('20210101')),
        partition p202102 values less than (TO_DAYS('20210201')),
        partition p202103 values less than (TO_DAYS('20210301')),
        partition p202104 values less than (TO_DAYS('20210401')),
        partition p202105 values less than (TO_DAYS('20210501')),
        partition p202106 values less than (TO_DAYS('20210601')),
        partition p202107 values less than (TO_DAYS('20210701')),
        partition p202108 values less than (TO_DAYS('20210801')),
        partition p202109 values less than (TO_DAYS('20210901')),
        partition p202110 values less than (TO_DAYS('20211001')),
        partition p202111 values less than (TO_DAYS('20211101')),
        partition p202112 values less than (TO_DAYS('20211201')),
        partition p202201 values less than (TO_DAYS('20220101')),
        partition p202202 values less than (TO_DAYS('20220201')),
        partition p202203 values less than (TO_DAYS('20220301')),
        partition p202204 values less than (TO_DAYS('20220401')),
        partition p202205 values less than (TO_DAYS('20220501')),
        partition p202206 values less than (TO_DAYS('20220601')),
        partition p202207 values less than (TO_DAYS('20220701')),
        partition p202208 values less than (TO_DAYS('20220801')),
        partition p202209 values less than (TO_DAYS('20220901')),
        partition p202210 values less than (TO_DAYS('20221001')),
        partition p202211 values less than (TO_DAYS('20221101')),
        partition p202212 values less than (TO_DAYS('20221201'))
    )"
            )*/
                ->create();
        }

        /**新闻数据表*/
        $printer->warning('安装数据库表：' . self::table_NEWS_POST);
        if (! $db->tableExist(self::table_NEWS_POST)) {
            $foreign_table = $db->getTable(self::table_NEWS);
            $db->createTable(
                self::table_NEWS_POST,
                '新闻数据表'
            )->addColumn(
                'post_id',
                Table::column_type_INTEGER,
                11,
                'NOT NULL',
                'POST ID'
            )->addColumn(
                'content',
                Table::column_type_TEXT,
                20000,
                'NOT NULL',
                '新闻内容'
            )->addColumn(
                'create_time',
                Table::column_type_DATETIME,
                '',
                'NOT NULL DEFAULT CURRENT_TIMESTAMP',
                '创建时间'
            )->addIndex(
                Table::index_type_DEFAULT,
                'post_id',
                'post_id'
            )->addConstraints(
                "foreign key (post_id) references {$foreign_table}(id) on delete cascade on update cascade,UNIQUE KEY(post_id)"
            )/*->addAdditional(
            "partition by range(post_id)(
    partition p1 values less than (100000),
    partition p2 values less than (200000),
    partition p3 values less than (300000),
    partition p4 values less than (400000),
    partition p5 values less than (500000),
    partition p6 values less than (600000),
    partition p7 values less than (700000),
    partition p8 values less than (800000),
    partition p9 values less than (900000),
    partition p10 values less than (1000000),
    partition p11 values less than (1100000),
    partition p12 values less than (1200000),
    partition p13 values less than (1300000),
    partition p14 values less than (1400000),
    partition p15 values less than (1500000),
    partition p16 values less than (1600000),
    partition p17 values less than (1700000),
    partition p18 values less than (1800000),
    partition p19 values less than (1900000),
    partition p20 values less than (2000000),
  partition p_end values less than MAXVALUE
)"
        )*/ ->create();
        }

        /**新闻用户表*/
        $printer->warning('安装数据库表：' . self::table_NEWS_USER);
        if (! $db->tableExist(self::table_NEWS_USER)) {
            $db->createTable(
                self::table_NEWS_USER,
                '新闻用户表'
            )->addColumn(
                'id',
                Table::column_type_INTEGER,
                11,
                'primary key NOT NULL AUTO_INCREMENT',
                '用户ID'
            )->addColumn(
                'name',
                Table::column_type_VARCHAR,
                12,
                'NOT NULL',
                '姓名'
            )->addColumn(
                'pw',
                Table::column_type_VARCHAR,
                18,
                'NOT NULL',
                '密码'
            )->addColumn(
                'email',
                Table::column_type_VARCHAR,
                60,
                'NOT NULL',
                '邮箱'
            )->addColumn(
                'token',
                Table::column_type_TEXT,
                256,
                'NULL',
                '令牌'
            )->addColumn(
                'login_time',
                Table::column_type_DATETIME,
                '',
                'NULL',
                '登录时间'
            )->addColumn(
                'expire_time',
                Table::column_type_DATETIME,
                '',
                'NULL',
                'token过期时间'
            )->addColumn(
                'create_time',
                Table::column_type_DATETIME,
                '',
                'NOT NULL DEFAULT CURRENT_TIMESTAMP',
                '创建时间'
            )->addIndex(
                Table::index_type_DEFAULT,
                'id',
                'id'
            )->addIndex(
                Table::index_type_FULLTEXT,
                'name',
                'name'
            )->create();
        }
    }
}
