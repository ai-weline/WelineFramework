<?php

/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/7
 * 时间：21:41
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Installer\Helper;
class InstallData
{
    function getData(string $key = '')
    {
        $data = [
            'env' => [
                'functions' => ['exec'],
                'modules' => [/*'file_info'*/],
                'commands'=>[
                    'bin/m module:command:upgrade',
                    'bin/m module:upgrade',
                    'bin/m deploy:mode:set dev',
                ]
            ],
            'db' => [
                'tables' => [
                    'core_menu' => 'CREATE TABLE core_menu
(
id int primary key comment "菜单ID",
p_id int default 0 comment "父级ID",
name varchar(20) comment "菜单",
action varchar(255) comment "菜单动作",
module varchar(20)  comment "模块"
)'
                ]
            ]
        ];
        return isset($data[$key]) ? $data[$key] : $data;
    }
}