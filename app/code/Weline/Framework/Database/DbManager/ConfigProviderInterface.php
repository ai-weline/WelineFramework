<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/6/15
 * 时间：16:00
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Database\DbManager;


interface ConfigProviderInterface
{
    /**
     * @DESC         |设置数据库类型
     *
     * 参数区：
     *
     * @param string $type
     * @return ConfigProviderInterface
     */
    function setDbType(string $type):ConfigProviderInterface;

    /**
     * @DESC         |数据库类型
     *
     * 参数区：
     *
     * @return string
     */
    function getDbType():string;

    /**
     * @DESC         |设置数据库主机
     *
     * 参数区：
     *
     * @param string $hostname
     * @return ConfigProviderInterface
     */
    function setHostName(string $hostname):ConfigProviderInterface;

    /**
     * @DESC         |数据库主机
     *
     * 参数区：
     *
     * @return string
     */
    function getHostName():string;

    /**
     * @DESC         |设置数据库名
     *
     * 参数区：
     *
     * @param string $database
     * @return ConfigProviderInterface
     */
    function setDatabase(string $database):ConfigProviderInterface;

    /**
     * @DESC         |数据库名
     *
     * 参数区：
     *
     * @return string
     */
    function getDatabase():string;

    /**
     * @DESC         |设置用户名
     *
     * 参数区：
     *
     * @param string $username
     * @return ConfigProviderInterface
     */
    function setUsername(string $username):ConfigProviderInterface;

    /**
     * @DESC         |用户名
     *
     * 参数区：
     *
     * @return string
     */
    function getUsername():string;

    /**
     * @DESC         |设置密码
     *
     * 参数区：
     *
     * @param string $password
     * @return ConfigProviderInterface
     */
    function setPassword(string $password):ConfigProviderInterface;

    /**
     * @DESC         |密码
     *
     * 参数区：
     *
     * @return string
     */
    function getPassword():string;

    /**
     * @DESC         |设置主机端口
     *
     * 参数区：
     *
     * @param string $host_port
     * @return ConfigProviderInterface
     */
    function setHostPort(string $host_port):ConfigProviderInterface;

    /**
     * @DESC         |主机端口
     *
     * 参数区：
     *
     * @return string
     */
    function getHostPort():string;

    /**
     * @DESC         | 设置表前缀
     *
     * 参数区：
     *
     * @param string $prefix
     * @return ConfigProviderInterface
     */
    function setPrefix(string $prefix):ConfigProviderInterface;

    /**
     * @DESC         |表前缀
     *
     * 参数区：
     *
     * @return string
     */
    function getPrefix():string;

    /**
     * @DESC         | 设置默认连接字符集
     *
     * 参数区：
     *
     * @param string $charset
     * @return ConfigProviderInterface
     */
    function setCharset(string $charset = 'utf8mb4'):ConfigProviderInterface;

    /**
     * @DESC         |连接字符集
     *
     * 参数区：
     *
     * @return string
     */
    function getCharset():string;


    /**
     * @DESC         | 设置连接项
     *
     * 参数区：
     *
     * @param array $pdo_options
     * @return ConfigProviderInterface
     */
    function setOptions(array $pdo_options = []):ConfigProviderInterface;

    /**
     * @DESC         |连接项
     *
     * 参数区：
     *
     * @return array
     */
    function getOptions():array;


}