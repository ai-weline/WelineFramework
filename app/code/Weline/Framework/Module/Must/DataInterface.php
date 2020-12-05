<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Module\Must;

interface DataInterface
{
    const file_Register = 'register.php';

    const file_etc_Env = 'etc/env.php';

    const dir_Etc = 'etc';

    const files = [
        self::file_Register,
        self::file_etc_Env,
    ];

    const dirs = [
        self::dir_Etc,
    ];
}
