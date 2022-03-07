<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache;

use Weline\Framework\App;
use Weline\Framework\App\Env;
use Weline\Framework\Manager\ObjectManager;
use function PHPUnit\Framework\isInstanceOf;

class CacheFactory
{
    const driver_NAMESPACE = Env::framework_name . '\\Framework\\Cache\\Driver\\';

    private static CacheFactory $instance;

    private array $config;

    private string $identity;

    private ?CacheInterface $driver=null;

    // FIXME 缓存清理后首次存储缓存set后 立即读取get 将返回false 待后期处理 先直接返回结果

    public function __construct(string $identity = 'cache_system')
    {
        $this->config   = App::Env('cache');
        $this->identity = $identity;
    }

    function __wakeup(){
        if(empty($this->driver)){
            $this->config   = (array)Env::getInstance()->getConfig('cache');
            $this->driver = $this->create();
        }
    }

    /**
     * @DESC         |创建缓存
     *
     * 参数区：
     *
     * @param string $driver
     * @param string $driver_class
     *
     * @return CacheInterface
     */
    public function create(string $driver = '',string $driver_class=''): CacheInterface
    {
        if (empty($driver) && isset($this->config['default'])) {
            $driver = $this->config['default'];
        }
        $driver_class = self::driver_NAMESPACE . ucfirst($driver);
        $this->driver = new $driver_class($this->identity, $this->config['drivers'][$driver]);
        return $this->driver;
    }
}
