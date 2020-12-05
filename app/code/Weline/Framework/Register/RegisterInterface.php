<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Register;

interface RegisterInterface
{
    const NAMESPACE = 'Weline\Framework\Register\\';

    const MODULE = 'module';

    const HOOK = 'hook';

    const LANGUAGE = 'language';

    const OBSERVER = 'observer';

    const THEME = 'theme';

    const PLUGIN = 'plugin';

    const ROUTER = 'router';

    const COMMAND = 'command';

    const register_file = 'register.php';

    /**
     * @DESC         |注册模块
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $type
     * @param $param
     * @param string|null $version
     * @param string $description
     */
    public static function register(string $type, $param, string $version = '', string $description = '');
}
