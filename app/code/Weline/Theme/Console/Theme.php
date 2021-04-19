<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console;

use Weline\Framework\Console\CommandInterface;
use Weline\Framework\Output\Cli\Printing;
use Weline\Theme\Model\WelineTheme;

class Theme implements CommandInterface
{
    /**
     * @var WelineTheme
     */
    private WelineTheme $welineTheme;

    /**
     * @var Printing
     */
    private Printing $printing;

    /**
     * Theme 初始函数...
     * @param WelineTheme $welineTheme
     * @param Printing $printing
     */
    public function __construct(
        WelineTheme $welineTheme,
        Printing $printing
    ) {
        $this->welineTheme = $welineTheme;
        $this->printing    = $printing;
    }

    public function execute($args = [])
    {
        $theme = $this->welineTheme->getActiveTheme();
        $this->printing->note(__('当前主题：%1', [$theme->getName()]));
        $this->printing->note(__('路径：%1', [$theme->getPath()]));
    }

    public function getTip(): string
    {
        return '查看当前主题';
    }
}
