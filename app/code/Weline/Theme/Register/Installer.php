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

class Installer implements RegisterInterface
{

    public function register($data, string $version = '', string $description = '')
    {
       p($data);
    }
}