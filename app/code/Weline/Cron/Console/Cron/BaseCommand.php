<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/26 23:50:43
 */

namespace Weline\Cron\Console\Cron;

use Weline\Backend\Model\Config;
use Weline\Cron\Schedule\Schedule;
use Weline\Framework\App\System;
use Weline\Framework\Output\Cli\Printing;

abstract class BaseCommand implements \Weline\Framework\Console\CommandInterface
{
    const cron_config_key = 'CRON_SCHEDULE_NAME';

    /**
     * @var \Weline\Backend\Model\Config
     */
    protected Config $config;
    /**
     * @var \Weline\Framework\Output\Cli\Printing
     */
    protected Printing $printing;
    /**
     * @var \Weline\Cron\Schedule\Schedule
     */
    protected Schedule $schedule;

    function __construct(
        Config   $config,
        Printing $printing,
        Schedule $schedule
    )
    {
        $this->config   = $config;
        $this->printing = $printing;
        $this->schedule = $schedule;
    }

    function getCronName(string $module_name='Weline_Cron')
    {
        $cron_name = $this->config->getConfig(self::cron_config_key, $module_name);
        if (empty($cron_name)) {
            $cron_name = '['.$module_name.']-' . md5(time() . mt_rand(0, 1000000));
            $this->config->setConfig(self::cron_config_key, $cron_name, $module_name);
        }
        return $cron_name;
    }
}