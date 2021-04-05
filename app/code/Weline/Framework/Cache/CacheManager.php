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
        if (empty($driver) && isset($this->config['default'])) {
            $driver = $this->config['default'];
        }
        $driver_class = self::driver_NAMESPACE . ucfirst($driver);

        return new $driver_class($this->identity, $this->config['drivers'][$driver]);
    }
}
