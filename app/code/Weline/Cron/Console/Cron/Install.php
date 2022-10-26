<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/26 21:32:48
 */

namespace Weline\Cron\Console\Cron;

use Weline\Framework\App\Env;

class Install extends BaseCommand
{

    /**
     * @inheritDoc
     */
    public function execute(array $args = [], array $data = [])
    {
        if (IS_WIN) {
            $cron_name = $this->config->getConfig(self::cron_config_key, $data['module']);
            if (empty($cron_name)) {
                $cron_name = '[' . $data['module'] . ']-' . md5(time() . mt_rand(0, 1000000));
                $this->config->setConfig(self::cron_config_key, $cron_name, $data['module']);
            }
            # 查找任务
            $data = $this->system->win_exec("schtasks /query /tn $cron_name");
            if (count($data['output']) !== 5) {
                $base_project_dir       = BP;
                $base_project_disk_name = substr($base_project_dir, 0, 2);
                # FIXME bat弹窗问题
                $bat_string             = "
@echo off
Rem WelineFramework框架 Window计划任务脚本
$base_project_disk_name && cd $base_project_dir && php bin/m cron:runscheduletask
                ";
                $bat_file               = Env::path_framework_generated . 'cron.bat';
                file_put_contents($bat_file, $bat_string);
                $create_command = "SCHTASKS /Create /TN $cron_name /TR $bat_file /SC MINUTE";
                $this->system->win_exec($create_command);
            }
        } else {
            // FIXME linux计划任务脚本
        }
        $this->printing->note('[' . PHP_OS . ']'.__('定时任务已安装：%1',$cron_name));
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '安装定时任务。';
    }
}