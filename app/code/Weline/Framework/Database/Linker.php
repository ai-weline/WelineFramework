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
use PhpParser\Node\Expr\Cast\Object_;
use Weline\Framework\Database\DbManager\ConfigProvider;
use Weline\Framework\Database\Exception\LinkException;
use Weline\Framework\Database\Linker\QueryAdapter;
use Weline\Framework\Database\Linker\QueryInterface;
use Weline\Framework\Manager\ObjectManager;

class Linker
{
    protected ?PDO $linker = null;
    protected ConfigProvider $configProvider;
    protected ?QueryInterface $query = null;

    /**
     * Linker 初始函数...
     * @param ConfigProvider $configProvider
     */
    function __construct(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
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
        return array('configProvider');
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
     * @DESC         |框架初始化函数
     *
     * 参数区：
     *
     * @throws LinkException
     * @throws \Weline\Framework\App\Exception
     */
    public function __init(): void
    {
        /* 1、初始化linker连接*/
        if (!$this->linker) {
            $this->linker = $this->getLink();
        }

        /* 2、初始化查询 */
        if (!$this->query) {
            $this->getQuery();
        }
    }

    /**
     * @DESC         |获取连接
     *
     * 参数区：
     *
     * @return PDO
     * @throws LinkException
     * @throws \Weline\Framework\App\Exception
     */
    private function getLink(): PDO
    {
        $db_type = $this->configProvider->getDbType();
        $dsn = "{$db_type}:host={$this->configProvider->getHostName()}:{$this->configProvider->getHostPort()};dbname={$this->configProvider->getDatabase()};charset={$this->configProvider->getCharset()}";
        if (!in_array($db_type, PDO::getAvailableDrivers())) {
            throw new LinkException(__('驱动不存在：%1,可用驱动列表：%2，更多驱动配置请转到php.ini中开启。', [$db_type, implode(',', PDO::getAvailableDrivers())]));
        }
        try {
            //初始化一个linker对象
            return new PDO($dsn, $this->configProvider->getUsername(), $this->configProvider->getPassword(), $this->configProvider->getOptions());
        } catch (PDOException $e) {
            throw new LinkException($e->getMessage());
        }
    }

    /**
     * @DESC         |获取查询类
     *
     * 参数区：
     *
     * @return QueryInterface
     * @throws LinkException
     * @throws \Weline\Framework\App\Exception
     */
    public function getQuery(): QueryInterface
    {
        if (is_null($this->query)) {
            $adapter_class = $this->getAdapter();
            $this->query = new $adapter_class($this->getLink());
        }
        return $this->query;
    }

    function query(string $sql): bool|\PDOStatement
    {
        return $this->linker->query($sql);
    }

    /**
     * 获取适配器
     * @return string
     */
    function getAdapter(): string
    {
        return 'Weline\\Framework\\Database\\Linker\\Query\\' . ucfirst($this->configProvider->getDbType());
    }
}