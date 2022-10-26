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
use Weline\Framework\App\System;
use Weline\Framework\Output\Cli\Printing;

abstract class BaseCommand implements \Weline\Framework\Console\CommandInterface
{
    const cron_config_key = 'CRON_SCHEDULE_NAME';

    /**
     * @var \Weline\Framework\App\System
     */
    protected System $system;
    /**
     * @var \Weline\Backend\Model\Config
     */
    protected Config $config;
    /**
     * @var \Weline\Framework\Output\Cli\Printing
     */
    protected Printing $printing;

    function __construct(
        System   $system,
        Config   $config,
        Printing $printing
    )
    {
        $this->system   = $system;
        $this->config   = $config;
        $this->printing = $printing;
    }
}