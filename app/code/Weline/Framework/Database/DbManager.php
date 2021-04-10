<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Output\Cli\Printing;

/**
 * 文件信息
 * DESC:   |
 * 作者：   秋枫雁飞
 * 日期：   2020/7/2
 * 时间：   1:24
 * 网站：   https://bbs.aiweline.com
 * Email：  aiweline@qq.com
 */
class DbManager extends \think\DbManager
{
    public function __construct()
    {
        $db_conf = Env::getInstance()->getDbConfig();# Env配置对象内存常驻，无需担心配置多次操作env.php配置文件
        if (empty($db_conf)) {
            $db_conf = Env::getInstance()->reload()->getDbConfig();
            if (empty($db_conf)) {
                if ('cli' === PHP_SAPI) {
                    (new Printing())->error('请安装系统后操作:bin/m system:install', '系统');

                    throw new Exception('数据库尚未配置，请安装系统后操作:bin/m system:install');
                }

                throw new Exception('数据库尚未配置，请安装系统后操作:bin/m system:install');
            }
        }
        $this->setConfig($db_conf);
        parent::__construct();
    }

    /**
     * @DESC         |获得当前数据库连接信息
     *
     * 参数区：
     *
     * @return bool|mixed
     */
    public function getCurrentConfig()
    {
        $config = $this->getConfig();

        return isset($config['default']) && isset($config['connections']) ? $config['connections'][$config['default']] : $config;
    }

    /**
     * @DESC         |补充查询
     *
     * 参数区：
     *
     * @param string $sql
     * @return mixed
     */
    public function query(string $sql)
    {
        return parent::query($sql);
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param array $config
     * @return $this
     */
    public function setDbConfig($config): DbManager
    {
        /**
         * 重载方法
         */
        parent::setConfig($config);

        return $this;
    }
}
