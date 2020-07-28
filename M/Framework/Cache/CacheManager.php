<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/6
 * 时间：16:01
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Cache;


use M\Framework\App\Env;

class CacheManager
{
    const driver_NAMESPACE = 'M\\Framework\\Cache\\Driver\\';
    private static CacheManager $instance;
    private array $config;

    private function __clone()
    {
    }

    private function __construct()
    {
        $this->config = Env::getInstance()->getConfig('cache');
    }

    /**
     * @DESC         |获取实例
     *
     * 参数区：
     *
     * @return CacheManager
     */
    static function getInstance(): CacheManager
    {
        if (!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    /**
     * @DESC         |方法描述
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
        return new $driver_class($this->config['drivers'][$driver]);
    }
}