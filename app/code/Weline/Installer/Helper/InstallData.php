<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
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
                'modules'   => ['PDO', 'exif', 'fileinfo', 'xsl'],
            ],
            'commands' => [
                'bin'.DIRECTORY_SEPARATOR.'m command:upgrade',
                'bin'.DIRECTORY_SEPARATOR.'m module:upgrade',
            ]
        ];

        return $data[$key] ?: $data;
    }
}
