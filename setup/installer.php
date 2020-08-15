<?php

/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/3
 * 时间：21:49
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */


use M\Framework\Http\Request;
use M\Framework\Manager\ObjectManager;
use \M\Installer\Runner;
require 'check.php';
require 'setup.php';
$runner = new Runner();
switch ($_GET['action']) {
    case 'env':
        $data = $runner->checkEnv();
        echo json_encode(coverData($data));
        break;
    case 'db':
        $data = $runner->installDb();
        echo json_encode(coverData($data));
        break;
    case 'install':
        $data = $runner->systemInstall();
        echo json_encode(coverData($data));
        break;
    case 'init_env':
        // 配置生成
        $data = $runner->systemInit();
        echo json_encode(coverData($data));
        break;
    case 'install_ok':
        // 命令清理
        $data = $runner->systemCommands();
        echo json_encode(coverData($data));
        $file = new M\Framework\FileSystem\Io\File();
        $file->open(BP.'setup/install.lock',$file::mode_a_add);
        $file->close();
        break;
    default:
        return http_response_code(404);
}

function coverData(array $data): array
{
    $tmpD = [];
    $tmp['data'] = [];
    foreach ($data['data'] as $key => $datum) {
        $tmpD['name'] = $key;
        $tmpD['value'] = $datum;
        $tmp['data'][] = $tmpD;
    }
    $tmp['hasErr'] = $data['hasErr'];
    $tmp['msg'] = isset($data['msg']) ? $data['msg'] : '';
    return $tmp;
}