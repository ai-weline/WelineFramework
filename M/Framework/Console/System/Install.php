<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/15
 * 时间：20:48
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\System;


use M\Framework\Database\DbManager;
use M\Framework\Database\Setup\DataInterface;
use M\Installer\Runner;
use PDO;
use PDOException;

class Install extends \M\Framework\Console\CommandAbstract
{
    function __construct()
    {
        parent::__construct();

    }

    /**
     * @inheritDoc
     */
    public function execute($args = array())
    {
        $runner = new Runner();
        if (!is_file(BP . 'setup/install.lock')) {
            $this->printer->warning('系统已安装，请勿重复安装！', '系统');
            exit();
        }
        // 环境检测
        $this->printer->note('第一步：环境检测...', '系统');
        $checkResult = $runner->checkEnv();
        if ($checkResult['hasErr']) {
            $this->printer->error($checkResult, '检测错误！', '系统');
            exit();
        }
        // 参数检测
        $this->printer->note('第二步：参数检测...', '系统');
        $args_config = [];
        foreach ($args as $arg) {
            // 数据库配置
            if (strstr($arg, '--db-')) {
                $kv_arr = explode('=', str_replace('--db-', '', $arg));
                if (count($kv_arr) != 2) {
                    $this->printer->error('错误的参数格式：' . $arg);
                    exit();
                }
                $args_config['db'][$kv_arr[0]] = $kv_arr[1];
            }
        }
        array_shift($args);
        $db_keys = DataInterface::db_keys;
        if (!isset($args_config['db'])) {
            $this->printer->error('数据库配置为空！示例：bin/m system:install --db-type=mysql', '系统');
            foreach ($db_keys as $item) $this->printer->printing($item, '--db-');
            exit();
        }
        $db_config = isset($args_config['db']) ? $args_config['db'] : [];
        $db_config = array_intersect_key($db_config, $db_keys);
        isset($db_config['type']) ? $db_config['type'] : $db_config['type'] = 'mysql';
        isset($db_config['hostport']) ? $db_config['hostport'] : $db_config['hostport'] = '3306';
        isset($db_config['prefix']) ? $db_config['prefix'] : $db_config['prefix'] = 'm_';
        isset($db_config['charset']) ? $db_config['charset'] : $db_config['charset'] = 'utf8';
        foreach ($db_keys as $db_key => $v) {
            if (!isset($db_config[$db_key])) {
                $this->printer->error('数据库' . $db_key . '配置不能为空！示例：bin/m system:install --db-' . $db_key . '=demo', '系统');
                exit();
            }
        }
        foreach ($db_config as $key => $item) {
            echo $this->printer->colorize(str_pad($key, 8, ' ', STR_PAD_LEFT), $this->printer::WARNING) . '=>' . $this->printer->colorize($item, $this->printer::NOTE) . "\r\n";
        }
        $this->printer->success('参数检测通过！', 'OK');
        $this->printer->note('第三步：配置安装...', '系统');
        $runner->installDb($db_config);
        $this->printer->note('第四步：数据安装...', '系统');
        $runner->systemInstall();
        $this->printer->note('第五步：系统命令更新...', '系统');
        $runner->systemCommands();
        $this->printer->note('第六步：系统初始化...', '系统');
        $initData['admin'] = 'admin_' . uniqid();
        $initData['api_admin'] = 'api_' . uniqid();
        $runner->systemInit($initData);
        $this->printer->note('初始化数据...', '系统');
        $this->printer->success(str_pad('admin后台入口:', 20, " ", STR_PAD_LEFT) . $initData['admin']);
        $this->printer->success(str_pad('Api后台入口:', 20, " ", STR_PAD_LEFT) . $initData['api_admin']);
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '框架安装';
    }
}