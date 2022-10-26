<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/27 00:51:30
 */

namespace Weline\Cron\Console\Cron\Task;

class Run extends \Weline\Cron\Console\Cron\BaseCommand
{

    /**
     * @inheritDoc
     */
    public function execute(array $args = [], array $data = [])
    {
        file_put_contents(__DIR__.DS.'tt.txt', '111111',FILE_APPEND);
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '运行计划调度任务';
    }
}