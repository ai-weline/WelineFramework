<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Installer\Helper;

class InstallData
{
    public function getData(string $key = '')
    {
        $data = [
            'env' => [
                'functions' => ['exec', 'putenv'],
                'modules' => ['PDO', 'exif', 'fileinfo', 'xsl'],
            ],
            'commands' => [
                'bin/m command:upgrade',
                'bin/m module:upgrade',
                'bin/m deploy:mode:set dev',
                'bin/m cache:clear',
            ],
            'db' => [
                'tables' => [
                    'm_core_menu' => 'CREATE TABLE m_core_menu
(
id int primary key comment "菜单ID",
p_id int default 0 comment "父级ID",
name varchar(20) comment "菜单",
action varchar(255) comment "菜单动作",
module varchar(20)  comment "模块"
)',
                ],
            ],
        ];

        return $data[$key] ?? $data;
    }
}
