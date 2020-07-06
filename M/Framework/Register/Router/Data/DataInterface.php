<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/20
 * 时间：13:34
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Register\Router\Data;


use M\Framework\Register\RegisterInterface;

interface DataInterface
{
    const register_file = 'register.php';
    const register_param = [
        'type' => null,
        'module' => null,
        'router' => null,
        'class' => null,
        'method' => null,
        'request_method' => null,
    ];

    const type_API = 'api';
    const type_PC = 'pc';
}