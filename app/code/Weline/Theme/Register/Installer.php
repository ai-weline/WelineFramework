<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Register;

use Weline\Framework\Register\RegisterInterface;
use Weline\Theme\Model\WelineTheme;

class Installer implements RegisterInterface
{
    /**
     * @var WelineTheme
     */
    private WelineTheme $welineTheme;

    public function __construct(
        WelineTheme $welineTheme
    ) {





        $this->welineTheme = $welineTheme;
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
       p(func_get_args());
    }
}
