<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/26 21:34:00
 */

namespace Weline\Cron\Console\Cron;

class Remove extends BaseCommand
{

    /**
     * @inheritDoc
     */
    public function execute(array $args = [], array $data = [])
    {
        $cron_name = $this->config->getConfig(self::cron_config_key, $data['module']);
        if ($cron_name) {
            if (IS_WIN) {
                # 查找任务
                $data = $this->system->win_exec("schtasks /query /tn $cron_name");
                if (count($data['output']) === 5) {
                    $data = $this->system->win_exec("schtasks /Delete /tn $cron_name /F");
                    if (count($data['output']) === 1) {
                        $this->printing->success('[' . PHP_OS . '] ' . __('系统计划任务：%1 ,成功删除!', $cron_name));
                    } else {
                        $this->printing->error('[' . PHP_OS . '] ' . __('系统计划任务%1移除失败！', $cron_name));
                    }
                } else {
                    $this->printing->error('[' . PHP_OS . '] ' . __('系统计划任务%1不存在！', $cron_name));
                }
            }else{
                // FIXME linux移除任务
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '移除定时任务。';
    }
}