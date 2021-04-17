<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/4/17
 * 时间：17:01
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
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

    function __construct(
        WelineTheme $welineTheme
    )
    {
        $this->welineTheme = $welineTheme;
    }

    public function register($data, string $version = '', string $description = '')
    {
       p(func_get_args());
    }
}