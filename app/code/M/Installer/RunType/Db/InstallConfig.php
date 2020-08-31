<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/7
 * 时间：22:06
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Installer\RunType\Db;


use M\Framework\App\Env;
use M\Framework\App\Exception;
use M\Framework\Database\DbManager;
use M\Framework\Database\Setup\DataInterface;
use M\Framework\Http\Request;
use M\Framework\Output\Cli\Printing;
use M\Framework\Setup\Data\Setup as DataSetup;
use M\Installer\Helper\Data;
use PDO;
use PDOException;

class InstallConfig
{
    protected Data $helper;
    protected Printing $printer;

    function __construct()
    {
        $this->helper = new Data();
        $this->printer = new Printing();
    }

    function run(array $params): array
    {
        $msg = '-------  数据库配置安装...  -------';
        $hasErr = false;
        if (empty($params)) {
            $hasErr = true;
            $msg = '异常的$params参数';
            if (CLI) {
                $this->printer->error($msg, 'ERROR');
                exit();
            };
        }
        unset($params['action']);
        $params['type'] = 'mysql';
        // 参数检测
        if (CLI) $this->printer->note('数据库：1、参数检测...', '系统');
        $db_keys = DataInterface::db_keys;
        $paramsCheck = array_intersect_key($params, $db_keys);
        foreach ($db_keys as $db_key => $v) {
            if (!isset($paramsCheck[$db_key])) {
                $hasErr = true;
                if (CLI) {
                    $msg = '数据库' . $db_key . '配置不能为空！示例：bin/m system:install --db-' . $db_key . '=demo';
                    $this->printer->error($msg, '系统');
                    exit();
                }
            }
        }
        // 数据库链接检测
        $db_conf = array(
            'default' => $params['type'],
            'connections' =>
                array(
                    'mysql' => $params
                ),
        );
        if (CLI) $this->printer->note('数据库：2、数据库链接检测...', '系统');
        try {
            //初始化一个PDO对象
            $dbh = new PDO($params['type'] . ':host=' . $params['hostname'] . ';dbname=' . $params['database'], $params['username'], $params['password']);
            if (CLI) $this->printer->success('PDO数据库链接检测通过', 'OK');
            $dbh = null;
        } catch (PDOException $e) {
            $this->printer->error('PDO数据库链接检测失败' . "Error!: " . $e->getMessage(), 'ERROR');
            exit();
        }
        // 数据库信息安装
        if (CLI) $this->printer->note('数据库：3、数据库信息安装...', '系统');
        try {
            Env::getInstance()->setConfig('db', $db_conf);
            $msg = '数据库安装初始化成功';
            if (CLI) {
                $this->printer->success($msg, 'OK');
            }
        } catch (Exception $exception) {
            $hasErr = true;
            $msg = '数据库安装初始化失败';
            if (CLI) {
                $this->printer->error($msg, 'ERROR');
                exit();
            }
        }
        return ['data' => $db_conf, 'hasErr' => $hasErr, 'msg' => $msg];
    }
}