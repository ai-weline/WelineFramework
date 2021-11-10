<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache;

use Weline\Framework\App\Env;
use Weline\Framework\Cache\Driver\DriverInterface;

class CacheFactory
{
    const driver_NAMESPACE = Env::framework_name . '\\Framework\\Cache\\Driver\\';

    private static CacheFactory $instance;

    private array $config;

    private string $identity;

    private DriverInterface $driver;

    // FIXME 缓存清理后首次存储缓存set后 立即读取get 将返回false 待后期处理 先直接返回结果

    public function __construct(string $identity = 'cache_system')
    {
        $this->config   = (array)Env::getInstance()->getConfig('cache');
        $this->identity = $identity;
    }

    /**
     * @DESC         |创建缓存
     *
     * 参数区：
     * @param string $driver
     * @return CacheInterface
     */
    public function create(string $driver = 'file'): CacheInterface
    {
        if(isset($this->driver)){
            return $this->driver->setIdentity($this->identity);
        }
        if (empty($driver) && isset($this->config['default'])) {
            $driver = $this->config['default'];
        }
        $driver_class = self::driver_NAMESPACE . ucfirst($driver);
        $this->driver = new $driver_class($this->identity, $this->config['drivers'][$driver]);
        return $this->driver;
    }
}
