<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/30 02:08:35
 */

namespace Aiweline\Demo\Cron\Demo;

class Run implements \Weline\Cron\CronTaskInterface
{


    /**
     * @inheritDoc
     */
    function name(): string
    {
        return 'demo_run';
    }

    /**
     * @inheritDoc
     */
    function tip(): string
    {
        return 'demo调度任务-二级目录测试';
    }

    /**
     * @inheritDoc
     */
    function cron_time(): string
    {
        return '*/2 * * * *';
    }

    /**
     * @inheritDoc
     */
    function execute(): string
    {
        file_put_contents(__DIR__ .DS.'tt.txt', '1',FILE_APPEND);
        return 'ok';
    }

    /**
     * @inheritDoc
     */
    public function unlock_timeout(int $minute = 30): int
    {
        return 1;
    }
}