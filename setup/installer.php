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


use M\Framework\Manager\ObjectManager;

require 'check.php';
require 'setup.php';
switch ($_GET['action']) {
    case 'env':
        $data = ObjectManager::make(\M\Installer\Runner::class, 'checkEnv', [
            \M\Installer\RunType\Env\Checker::class
        ]);
        echo json_encode(coverData($data));
        break;
    case 'db':
        $data = ObjectManager::make(\M\Installer\Runner::class, 'installDb', [
            \M\Installer\RunType\Db\Installer::class
        ]);
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