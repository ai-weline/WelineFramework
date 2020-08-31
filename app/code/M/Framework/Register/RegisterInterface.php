<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/20
 * 时间：13:28
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Register;


interface RegisterInterface
{
    const NAMESPACE = 'M\Framework\Register\\';

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
    static function register(string $type, $param, string $version = '', string $description = '');
}