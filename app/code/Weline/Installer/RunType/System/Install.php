<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Installer\RunType\System;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Setup\Data\Setup as DataSetup;
use Weline\Installer\Helper\Data;

class Install
{
    protected Data $data;

    protected Printing $printer;

    public function __construct()
    {
        $this->data    = new Data();
        $this->printer = new Printing();
    }

    public function run()
    {
        $db_conf = Env::getInstance()->getDbConfig();
        if (empty($db_conf)) {
            throw  new Exception('请先安装数据库配置！');
        }
        $setup = new DataSetup();
        $db    = $setup->getDb();

        $tables = $this->data->getDbTables();
        $tmp    = [];
        $hasErr = false;
        foreach ($tables as $table => $createSql) {
            if ($db->tableExist($table)) {
                if (CLI) {
                    $this->printer->warning('删除表：' . $table);
                }
                $db->dropTable($table);
            }

            try {
                if (CLI) {
                    $this->printer->note('新增表：' . $table);
                }
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
