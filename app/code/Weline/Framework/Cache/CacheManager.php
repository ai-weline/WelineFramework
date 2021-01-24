<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache;

use Weline\Framework\App\Env;

class CacheManager
{
    const driver_NAMESPACE = Env::framework_name . '\\Framework\\Cache\\Driver\\';

    private static CacheManager $instance;

    private array $config;
    private string $identity;

    function __construct(string $identity = '')
    {
        $this->config = Env::getInstance()->getConfig('cache');
        $this->identity = $identity;
    }

    /**
     * @DESC         |创建缓存
     *
     * 参数区：
     * @param string $driver
     * @return CacheInterface
     */
    public function create(string $driver = ''): CacheInterface
    {
        if (empty($driver) && isset($this->config['default'])) {
            $driver = $this->config['default'];
        }
        $driver_class = self::driver_NAMESPACE . ucfirst($driver);

        return new $driver_class($this->identity, $this->config['drivers'][$driver]);
    }
}
