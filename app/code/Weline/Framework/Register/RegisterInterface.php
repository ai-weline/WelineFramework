<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Register;

interface RegisterInterface
{
    const NAMESPACE = 'Weline\\Framework\\Register\\';

    const MODULE = 'module';

    const I18N = 'i18n';

    const ROUTER = 'router';

    const THEME = 'theme';

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
     * @param $data
     * @param string|null $version
     * @param string $description
     */
    public function register($data, string $version = '', string $description = '');
}
