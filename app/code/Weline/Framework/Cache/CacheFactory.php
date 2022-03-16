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
    private string $tip;
    private string $status;

    private ?CacheInterface $driver = null;

    // FIXME 缓存清理后首次存储缓存set后 立即读取get 将返回false 待后期处理 先直接返回结果

    /**
     * @param string $identity    [缓存识别]
     * @param bool   $permanently [持久使用]
     * @param string $tip         【说明】
     */
    public function __construct(string $identity = 'cache_system', string $tip = '', bool $permanently = false)
    {
        $this->config   = App::Env('cache');
        $this->identity = $identity;
        $this->tip      = $tip;
        $this->status   = $permanently;
    }

    function __wakeup()
    {
        if (empty($this->driver)) {
            $this->config = (array)Env::getInstance()->getConfig('cache');
            $this->driver = $this->create();
        }
    }

    /**
     * @DESC         |创建缓存
     *
     * 参数区：
     *
     * @param string $driver [驱动名|驱动类]
     * @param string $tip    [缓存说明]
     *
     * @return CacheInterface
     */
    public function create(string $driver = '',  string $tip = null): CacheInterface
    {
        if (empty($driver) && isset($this->config['default'])) {
            $driver = $this->config['default'];
        }
        if (class_exists($driver)) {
            $driver_class = $driver;
        } else {
            $driver_class = self::driver_NAMESPACE . ucfirst($driver);
        }
        $status = (bool)Env::getInstance()->getData('cache/status/' . $this->identity);
        $this->driver = new $driver_class($this->identity, $this->config['drivers'][$driver], $tip ?? $this->tip, $status??$this->status);
        return $this->driver;
    }

}
