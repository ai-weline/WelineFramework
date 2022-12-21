<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/26 21:28:43
 */

namespace Weline\Cron\Console\Cron;

use Weline\Backend\Model\Config;
use Weline\Framework\App\System;

class Run extends BaseCommand
{
    /**
     * @inheritDoc
     */
    public function execute(array $args = [], array $data = [])
    {
        $cron_name = $this->getCronName($data['module']);
        $result    = $this->schedule->run($cron_name);
        if ($result['status']) {
            $this->printing->success($result['msg']);
        } else {
            $this->printing->error($result['msg']);
        }
    }

    /**
     * @inheritDoc
     */
    public function tip(): string
    {
        return '运行系统定时任务。';
    }
}
