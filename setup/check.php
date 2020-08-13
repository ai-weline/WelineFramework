<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/6
 * 时间：21:19
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */
// 安装检测
if (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'install.lock')) /*Header("Location: /setup/")*/ {
    http_response_code(404);
    exit(0);
}