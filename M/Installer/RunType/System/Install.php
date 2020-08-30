<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/16
 * 时间：1:28
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Installer\RunType\System;


use M\Framework\App\Env;
use M\Framework\App\Exception;
use M\Framework\Setup\Data\Setup as DataSetup;
use M\Installer\Helper\Data;

class Install
{
    protected Data $data;

    function __construct()
    {
        $this->data = new Data();
    }

    function run()
    {
        $db_conf = Env::getInstance()->getDbConfig();
        if (empty($db_conf)) throw  new Exception('请先安装数据库配置！');
        $setup = new DataSetup();
        $db = $setup->getDb();

        $tables = $this->data->getDbTables();
        $tmp = [];
        $hasErr = false;
        foreach ($tables as $table => $createSql) {
            if($db->tableExist($table)){
                $db->
            }
            try {
                $db->query($createSql);
                $result = true;
            } catch (Exception $exception) {
                $hasErr = true;
                $result = false;
            }
//            $db->query('drop table ' . $table);
            $tmp['---install table "' . $table . '"'] = 'Create table ' . $table . ($result ? ' is success!(✔)' : ' is failed!(✖)');
        }
        return ['data' => $tmp, 'hasErr' => $hasErr, 'msg' => '-------  系统安装...  -------'];
    }
}