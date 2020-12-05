<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Setup\Data;

interface DataInterface
{
    const dir = 'Setup';

    const type_INSTALL = 'Install';

    const type_UPGRADE = 'Upgrade';

    const type_REMOVE = 'Remove';

    const upgrade_FILES = [
        self::type_UPGRADE,
    ];

    const install_FILES = [
        self::type_INSTALL,
    ];

    const remove_FILES = [
        self::type_REMOVE,
    ];
}
