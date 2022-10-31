<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/26 22:20:13
 */

namespace Weline\Cron\Model;

use Weline\Cron\Helper\CronStatus;
use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class CronTask extends \Weline\Framework\Database\Model
{
    const fields_NAME                 = 'name';
    const fields_MODULE               = 'module';
    const fields_CLASS                = 'class';
    const fields_TIP                  = 'tip';
    const fields_CRON_TIME            = 'cron_time';
    const fields_STATUS               = 'status';
    const fields_RUNTIME              = 'runtime';
    const fields_BLOCK_TIME           = 'block_time';
    const fields_BLOCK_TIMES          = 'block_times';
    const fields_BLOCK_UNLOCK_TIMEOUT = 'block_unlock_timeout';
    const fields_RUN_TIME             = 'run_time';
    const fields_RUN_DATE             = 'run_date';
    const fields_NEXT_RUN_DATE        = 'next_run_date';
    const fields_PRE_RUN_DATE         = 'pre_run_date';
    const fields_RUN_TIMES            = 'run_times';
    const fields_RUNTIME_ERROR        = 'runtime_error';
    const fields_RUNTIME_ERROR_DATE   = 'runtime_error_date';

    /**
     * @inheritDoc
     */
    public function setup(ModelSetup $setup, Context $context): void
    {
        $this->install($setup, $context);
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    public function install(ModelSetup $setup, Context $context): void
    {
        $setup->dropTable();
        if (!$setup->tableExist()) {
            $setup->createTable()
                  ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'primary key auto_increment', 'ID')
                  ->addColumn(self::fields_NAME, TableInterface::column_type_VARCHAR, 255, 'not null', '调度任务名')
                  ->addColumn(self::fields_MODULE, TableInterface::column_type_VARCHAR, 128, 'not null', '模组')
                  ->addColumn(self::fields_CLASS, TableInterface::column_type_VARCHAR, 255, 'not null', 'PHP调度类')
                  ->addColumn(self::fields_CRON_TIME, TableInterface::column_type_VARCHAR, 64, 'not null', '调度频率')
                  ->addColumn(self::fields_STATUS, TableInterface::column_type_VARCHAR, 8, 'default "' . CronStatus::PENDING->value . '"', '任务状态')
                  ->addColumn(self::fields_RUNTIME, TableInterface::column_type_FLOAT, 0, 'default 0', '运行时长')
                  ->addColumn(self::fields_BLOCK_TIME, TableInterface::column_type_FLOAT, 0, 'default 0', '阻塞时长')
                  ->addColumn(self::fields_BLOCK_TIMES, TableInterface::column_type_INTEGER, 0, 'default 0', '阻塞次数')
                  ->addColumn(self::fields_BLOCK_UNLOCK_TIMEOUT, TableInterface::column_type_INTEGER, 0, 'default 30', '阻塞超时解锁时长')
                  ->addColumn(self::fields_RUN_TIME, TableInterface::column_type_VARCHAR, 20, 'default 0', '运行时间戳')
                  ->addColumn(self::fields_RUN_DATE, TableInterface::column_type_DATETIME, 0, 'default null', '运行日期')
                  ->addColumn(self::fields_NEXT_RUN_DATE, TableInterface::column_type_DATETIME, 0, 'default null', '下次运行时间')
                  ->addColumn(self::fields_PRE_RUN_DATE, TableInterface::column_type_DATETIME, 0, 'default null', '上次运行时间')
                  ->addColumn(self::fields_RUN_TIMES, TableInterface::column_type_INTEGER, 0, 'default 0', '运行次数')
                  ->addColumn(self::fields_TIP, TableInterface::column_type_TEXT, 0, '', '任务描述')
                  ->addColumn(self::fields_RUNTIME_ERROR, TableInterface::column_type_TEXT, 0, '', '运行时错误')
                  ->addColumn(self::fields_RUNTIME_ERROR_DATE, TableInterface::column_type_DATETIME, 0, '', '运行时错误发生时间')
                  ->addIndex(TableInterface::index_type_UNIQUE, 'UNIQUE_TASK_NAME', self::fields_NAME)
                  ->create();
        }
    }
}