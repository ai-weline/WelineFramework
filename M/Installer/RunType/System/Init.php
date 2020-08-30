<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/16
 * 时间：2:38
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Installer\RunType\System;


use M\Framework\App\Exception;
use M\Framework\FileSystem\Io\File;

class Init
{
    function run(array $params)
    {
        if (!isset($params['admin']) || !isset($params['api_admin'])) throw new Exception('参数不完整！');
        $env_instance = \M\Framework\App\Env::getInstance();
        if (!is_file($env_instance::path_ENV_FILE)) throw new Exception('不存在的环境！');
        $env = require $env_instance::path_ENV_FILE;
        if (empty($env)) throw new Exception('环境为空！');
        $env['admin'] = $params['admin'];
        $env['api_admin'] = $params['api_admin'];
        $file = new File();
        $file->open($env_instance::path_ENV_FILE, $file::mode_w);
        $text = '<?php return ' . var_export($env, true) . ';';
        $file->write($text);
        $file->close();
        return ['data' => [
            'admin' => $params['admin'],
            'api_admin' => $params['api_admin']
        ], 'hasErr' => false, 'msg' => '-------  配置环境初始化...  -------'];
    }
}