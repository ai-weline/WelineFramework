<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/27 00:28:53
 */

namespace Weline\Cron\Console\Cron;

class RunScheduleTask extends BaseCommand
{

    /**
     * @inheritDoc
     */
    public function execute(array $args = [], array $data = [])
    {
        dd($data);
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '运行计划任务';
    }
}