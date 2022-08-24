<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache\Driver;

use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Cache\Driver\Memcached\Connection;
use Weline\Framework\Manager\ObjectManager;

class Memcached extends CacheDriverAbstract
{
    private Connection $connection;

    private function __clone()
    {
    }

    public function __init()
    {
        if (empty($config['options'])) {
            $config['options'] = [];
        }
        if (!isset($config['host']) && !isset($config['port']) && !isset($config['timeout']) && !isset($config['options'])) {
            throw new Exception(__('请指定memcached的配置项，示例：%1', "
            'memcached' =>
                        array(
                            'host' => '127.0.0.1',
                            'port' => '11211',
                            'timeout' => '100',
                            'options' => array(
                                'servers' => array('memcached.aiweline.com:11211'),//memcached 服务的地址、端口
                                'debug' => true,//是否打开debug
                                'compress_threshold' => 10240,//超过多少字节的数据时进行压缩
                                'persistant' => false//是否使用持久连接
                            )
                        ),"));
        }
        $this->connection = Connection::getInstance($config['host'], (int)$config['port'], (int)$config['timeout'], $config['options']);
    }

    /**
     * @return mixed|Connection|ObjectManager
     */
    public function getConnection(): mixed
    {
        return $this->connection;
    }


    public function get($key): mixed
    {
        return $this->connection->getData($key);
    }

    public function exists($key): mixed
    {
        if ($this->connection->getData($key)) {
            return true;
        }
        return false;
    }

    public function set($key, $value, int $duration = 1800): mixed
    {
        if (!$this->status) {
            return false;
        }
        $this->connection->getMemcached()->set($key, $value, $duration);
        return $this;
    }

    public function add($key, $value, int $duration = 1800): mixed
    {
        if (!$this->status) {
            return false;
        }
        $this->connection->getMemcached()->add($key, $value, $duration);
        return $this;
    }


    public function delete($key): mixed
    {
        $this->connection->getMemcached()->delete($key);
        return true;
    }

    public function flush(): bool
    {
        $this->connection->getMemcached()->flush();
        return true;
    }

    public function clear(): bool
    {
        $this->connection->getMemcached()->flushBuffers();
        return true;
    }
}
