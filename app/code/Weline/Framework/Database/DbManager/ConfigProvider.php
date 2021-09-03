<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/6/15
 * 时间：16:01
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Database\DbManager;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Output\Cli\Printing;

/**
 * 文件信息
 * DESC:   | 数据库配置者信息
 * 作者：   秋枫雁飞
 * 日期：   2021/6/15
 * 时间：   16:01
 * 网站：   https://bbs.aiweline.com
 * Email：  aiweline@qq.com
 * @DESC:    此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @since 1.2
 *
 * Class Configurator
 * @package Weline\Framework\Database\DbManager
 */
class ConfigProvider extends DataObject implements ConfigProviderInterface
{
    public string $connection_name = 'default';

    /**
     * @throws Exception
     */
    function __construct($db_conf = [])
    {
        if (empty($db_conf)) {
            try {
                $db_conf = $this->getConfig();
            } catch (Exception $e) {
                throw new Exception('数据库配置读取异常');
            }
        }
        parent::__construct($db_conf);
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return array|mixed
     * @throws Exception
     */
    protected function getConfig(): mixed
    {
        $db_conf = Env::getInstance()->getDbConfig();# Env配置对象内存生命周期常驻，无需担心配置多次操作env.php配置文件
        if (empty($db_conf)) {
            $db_conf = Env::getInstance()->reload()->getDbConfig();
            if (empty($db_conf) || !isset($db_conf['default']) || !isset($db_conf['connections'])) {
                if ('cli' === PHP_SAPI) {
                    (new Printing())->error('请安装系统后操作:bin/m system:install', '系统');

                    throw new Exception('数据库尚未配置，请安装系统后操作:bin/m system:install');
                }

                throw new Exception('数据库尚未配置，请安装系统后操作:bin/m system:install');
            }
        }
        $connection_name = $db_conf['default'];
        $this->setConnectionName($connection_name);
        return $db_conf['connections'][$connection_name];
    }


    function getConnectionName(): string
    {
        return $this->connection_name;
    }

    function setConnectionName($connection_name): ConfigProviderInterface
    {
        $this->connection_name = $connection_name;
        return $this;
    }

    function setDbType(string $type): ConfigProviderInterface
    {
        return $this->setData('type', $type);
    }

    function getDbType(): string|null
    {
        return $this->getData('type');
    }

    function setHostName(string $hostname): ConfigProviderInterface
    {
        return $this->setData('hostname', $hostname);
    }

    function getHostName(): string
    {
        return $this->getData('hostname');
    }

    function setDatabase(string $database_name): ConfigProviderInterface
    {
        return $this->setData('database', $database_name);
    }

    function getDatabase(): string
    {
        return $this->getData('database');
    }

    function setUsername(string $username): ConfigProviderInterface
    {
        return $this->setData('username', $username);
    }

    function getUsername(): string
    {
        return $this->getData('username');
    }

    function setPassword(string $password): ConfigProviderInterface
    {
        return $this->setData('password', $password);
    }

    function getPassword(): string
    {
        return $this->getData('password');
    }

    function setHostPort(string $host_port): ConfigProviderInterface
    {
        return $this->setData('hostport', $host_port);
    }

    function getHostPort(): string
    {
        return $this->getData('hostport');
    }

    function setPrefix(string $prefix): ConfigProviderInterface
    {
        return $this->setData('prefix', $prefix);
    }

    function getPrefix(): string
    {
        return $this->getData('prefix');
    }

    function setCharset(string $charset = 'utf8mb4'): ConfigProviderInterface
    {
        return $this->setData('charset', $charset);
    }

    function getCharset(): string
    {
        return $this->getData('charset') ?? 'utf8mb4';
    }

    function setOptions(array $pdo_options = []): ConfigProviderInterface
    {
        return $this->setData('options', $pdo_options);
    }

    function getOptions(): array
    {
        return $this->getData('options') ?? [];
    }
}