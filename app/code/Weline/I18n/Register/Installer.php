<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\I18n\Register;

use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Register\RegisterInterface;

class Installer implements RegisterInterface
{
    /**
     * @var Printing
     */
    private Printing $printing;

    /**
     * Installer 初始函数...
     * @param Printing $printing
     */
    public function __construct(
        Printing $printing
    ) {
        $this->printing    = $printing;
    }

    /**
     * @DESC         |注册主题
     *
     * 参数区：
     *
     * @param $data
     * @param string $version
     * @param string $description
     */
    public function register($data, string $version = '', string $description = '')
    {
        // 参数检查
        $this->printing->printing(__('语言包：%1已安装。', [$data]));
    }
}
