<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/6/15
 * 时间：16:43
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Database;


use PDO;
use PDOException;
use Weline\Framework\Database\Api\Linker\AlterInterface;
use Weline\Framework\Database\Api\Linker\QueryInterface;

use Weline\Framework\Database\DbManager\ConfigProvider;
use Weline\Framework\Database\Exception\LinkException;
use Weline\Framework\Manager\ObjectManager;

class LinkerFactory
{
    protected ?PDO $linker = null;
    protected ConfigProvider $configProvider;
    protected ?QueryInterface $query = null;
    protected ?AlterInterface $alter = null;

    /**
     * Linker 初始函数...
     * @param ConfigProvider $configProvider
     * @throws LinkException
     */
    function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
        $this->create();
    }

    /**
     * @DESC         |连接配置
     *
     * 参数区：
     *
     * @return ConfigProvider
     */
    function getConfigProvider(): ConfigProvider
    {
        return $this->configProvider;
    }

    /**
     * @DESC         |休眠时执行函数： 保存配置信息，以及模型数据
     *
     * 参数区：
     *
     * @return string[]
     */
    public function __sleep()
    {
        return array('configProvider', 'query');
    }

    /**
     * @DESC         |唤醒时执行函数
     *
     * 参数区：
     *
     * @throws LinkException
     * @throws \Weline\Framework\App\Exception
     */
    public function __wakeup()
    {
        $this->__init();
    }

    /**
     * @DESC          # 框架初始化函数
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 21:50
     * 参数区：
     * @throws LinkException
     */
    public function __init(): void
    {
        /* 1、初始化linker连接*/
        if (!$this->linker) {
            $this->create();
        }
    }

    /**
     * @DESC          # 获得数据库PDO链接
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 21:10
     * 参数区：
     * @throws LinkException
     */
    public function create(): static
    {
        $db_type = $this->configProvider->getDbType();
        $dsn = "{$db_type}:host={$this->configProvider->getHostName()}:{$this->configProvider->getHostPort()};dbname={$this->configProvider->getDatabase()};charset={$this->configProvider->getCharset()}";
        if (!in_array($db_type, PDO::getAvailableDrivers())) {
            throw new LinkException(__('驱动不存在：%1,可用驱动列表：%2，更多驱动配置请转到php.ini中开启。', [$db_type, implode(',', PDO::getAvailableDrivers())]));
        }
        try {
            //初始化一个linker对象
            $this->linker = new PDO($dsn, $this->configProvider->getUsername(), $this->configProvider->getPassword(), $this->configProvider->getOptions());
        } catch (PDOException $e) {
            throw new LinkException($e->getMessage());
        }
        return $this;
    }

    /**
     * @DESC          # 获取连接
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 21:06
     * 参数区：
     * @return PDO
     * @throws LinkException
     */
    public function getLink(): PDO
    {
        $this->__init();
        return $this->linker;
    }

    /**
     * @DESC          # 获取查询类
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/18 21:07
     * 参数区：
     * @return QueryInterface
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     */
    public function getQuery(): QueryInterface
    {
        if (is_null($this->query)) {
            $this->query = ObjectManager::getInstance($this->getAdapter());
        }
        return $this->query;
    }

    /**
     * @DESC          # 查询
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 22:40
     * 参数区：
     * @param string $sql
     * @return QueryInterface
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     */
    function query(string $sql): QueryInterface
    {
        return $this->getQuery()->query($sql);
    }
    /**
     * @DESC          # 获取修改者
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/5 21:11
     * 参数区：
     * @return AlterInterface
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     */
    public function getAlter(): AlterInterface
    {
        if (is_null($this->alter)) {
            $this->alter = ObjectManager::getInstance($this->getAdapter('alert'));
        }
        return $this->alter;
    }

    /**
     * 获取适配器
     * @param string $driver_type
     * @return string
     */
    function getAdapter(string $driver_type='Query'): string
    {
        $driver_type = ucfirst($driver_type);
        return "Weline\\Framework\\Database\\Linker\\{$driver_type}\\Adapter\\" . ucfirst($this->configProvider->getDbType());
    }
}