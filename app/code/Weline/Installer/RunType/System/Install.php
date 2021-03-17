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

    /**
     * @var Printing
     */
    private Printing $printing;
    /**
     * @var DataSetup
     */
    private DataSetup $setup;

    public function __construct(
        Data $data,
        DataSetup $setup,
        Printing $printing
    )
    {
        $this->data = $data;
        $this->printing = $printing;
        $this->setup = $setup;
    }

    public function run()
    {
        // 阻塞等待配置文件写入
        $break = false;
        $wait_times = 1;
        while (!$break) {
            sleep(1);
            $db_conf = Env::getInstance()->reload()->getDbConfig();
            if ($db_conf) {
                $break = true;
            }
            $wait_times += 1;
            if ($wait_times == 10) {
//                throw  new Exception('请先安装数据库配置！');
            }
        }


        $db = $this->setup->getDb();

        $tables = $this->data->getDbTables();
        $tmp = [];
        $hasErr = false;
        foreach ($tables as $table => $createSql) {
            if ($db->tableExist($table)) {
                if (CLI) {
                    $this->printing->warning('删除表：' . $table);
                }
                $db->dropTable($table);
            }

            try {
                if (CLI) {
                    $this->printing->note('新增表：' . $table);
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
