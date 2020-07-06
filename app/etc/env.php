<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/9
 * 时间：22:08
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */
return array(
    'db' => array(
        'default' => 'mysql',
        'connections' => [
            'mysql' => [
                // 数据库类型
                'type' => 'mysql',
                // 服务器地址
                'hostname' => '121.36.94.214',
                // 数据库名
                'database' => 'source_center',
                // 数据库用户名
                'username' => 'source_center',
                // 数据库密码
                'password' => 'api.news.aiweline.com',
                // 数据库连接端口
                'hostport' => '',
                // 数据库连接参数
                'options' => [PDO::ATTR_PERSISTENT => true],
                // 数据库编码默认采用utf8
                'charset' => 'utf8',
                // 数据库表前缀
                'prefix' => 'm_',
            ],
        ],
    ),
    'log' => array(
        'error' => BP . 'var/log/error.log',
        'exception' => BP . 'var/log/exception.log',
        'notice' => BP . 'var/log/notice.log',
        'warning' => BP . 'var/log/warning.log',
        'debug' => BP . 'var/log/debug.log',
    )
);