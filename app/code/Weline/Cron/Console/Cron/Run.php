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
        $cron_name = $this->config->getConfig(self::cron_config_key, $data['module']);
        if ($cron_name) {
            if (IS_WIN) {
                $data = $this->system->win_exec("schtasks /Run /tn $cron_name");
                if (count($data['output']) === 1) {
                    $this->printing->success('[' . PHP_OS . '] ' . __('系统计划任务：%1 ,成功运行!', $cron_name));
                } else {
                    $this->printing->error('[' . PHP_OS . '] ' . __('系统计划任务：%1 ,运行失败!任务可能未安装！请执行：php bin/m cron:install 安装计划任务！', $cron_name));
                }
            } else {
                // FIXME linux
            }
        } else {
            $this->printing->error('[' . PHP_OS . '] ' . __('系统计划任务：%1 ,尚未安装!', $cron_name));
        }

    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '运行计划任务。';
    }
}