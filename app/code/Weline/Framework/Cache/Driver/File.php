<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache\Driver;

use Weline\Framework\Cache\CacheInterface;

class File implements CacheInterface, DriverInterface
{
    private int $status;

    /**
     * @var string 存储缓存文件的目录。
     * 缓存文件的地址，例如/var/html/projects/var/cache/
     */
    public string $cachePath;

    public function __construct(string $identity, array $config)
    {
        if (isset($config['path']) && DIRECTORY_SEPARATOR === '\\') {
            $config['path'] = str_replace('/', DIRECTORY_SEPARATOR, $config['path']);
        }
        $this->cachePath = BP . $config['path'] . DIRECTORY_SEPARATOR . $identity . DIRECTORY_SEPARATOR ?? BP . 'var' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $identity . DIRECTORY_SEPARATOR;

        if (! is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0770, true);
        }
    }

    /**
     * @DESC         |获取状态
     * 0 : 关闭
     * 1 : 开启
     * 参数区：
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @DESC         |设置状态
     * 0 : 关闭
     * 1 : 开启
     * 参数区：
     *
     * @param int $status
     * @return CacheInterface
     */
    public function setStatus(int $status):CacheInterface
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @DESC         |从给定键生成规范化缓存键
     *
     * 参数区：
     *
     * @param $key
     * @return string
     */
    public function buildKey($key)
    {
        if (! is_string($key)) {
            // 不是字符串，json_encode转成字符串
            $key = json_encode($key);
        }

        return md5($key);
    }

    /**
     * @DESC         |使用指定键从缓存中检索值
     *
     * 参数区：
     *
     * @param $key
     * @return bool|mixed
     */
    public function get($key)
    {
        $key       = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // filemtime用来获取文件的修改时间
        if (@filemtime($cacheFile) > time()) {
            // file_get_contents用来获取文件内容，unserialize用来反序列化文件内容
            return unserialize(@file_get_contents($cacheFile));
        }

        return false;
    }

    /**
     * @DESC         |检查缓存中是否存在指定的键。
     *
     * 参数区：
     *
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        $key       = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // 用修改时间标记过期时间，存入时会做相应的处理
        return @filemtime($cacheFile) > time();
    }

    /**
     * @DESC         |使用指定的键从缓存中检索多个值。
     *
     * 参数区：
     *
     * @param $keys
     * @return array|mixed
     */
    public function getMulti($keys)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->get($key);
        }

        return $results;
    }

    /**
     * @DESC         |将键标识的值存储到缓存中。
     *
     * 参数区：
     *
     * @param $key
     * @param $value
     * @param int $duration
     * @return bool|mixed
     */
    public function set($key, $value, $duration = 0)
    {
        $key       = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // serialize用来序列化缓存内容
        $value = serialize($value);
        // file_put_contents用来将序列化之后的内容写入文件，LOCK_EX表示写入时会对文件加锁
        if (@file_put_contents($cacheFile, $value, LOCK_EX) !== false) {
            if ($duration <= 0) {
                // 不设置过期时间，设置为一年，这是因为用文件的修改时间来做过期时间造成的
                // redis/memcache 等都不会有这个问题
                $duration = 31536000; // 1 year
            }
            // touch用来设置修改时间，过期时间为当前时间加上$duration
            return touch($cacheFile, $duration + time());
        }

        return false;
    }

    /**
     * @DESC         |缓存中存储多个项目。每个项包含一个由键标识的值。
     *
     * 参数区：
     *
     * @param $items
     * @param int $duration
     * @return array|mixed
     */
    public function setMulti($items, $duration = 0)
    {
        $failedKeys = [];
        foreach ($items as $key => $value) {
            if ($this->set($key, $value, $duration) === false) {
                $failedKeys[] = $key;
            }
        }

        return $failedKeys;
    }

    /**
     * @DESC         |如果缓存不包含该键，则将由键标识的值存储到缓存中。
     *                如果缓存已包含密钥，则不会执行任何操作。
     *
     * 参数区：
     *
     * @param $key
     * @param $value
     * @param int $duration
     * @return bool|mixed
     */
    public function add($key, $value, $duration = 0)
    {
        //  key不存在，就设置缓存
        if (! $this->exists($key)) {
            return $this->set($key, $value, $duration);
        }

        return false;
    }

    /**
     * @DESC         |在缓存中存储多个项目。每个项包含一个由键标识的值。
     *                如果缓存已经包含这样一个键，则现有值和过期时间将被保留。
     *
     * 参数区：
     *
     * @param $items
     * @param int $duration
     * @return array|mixed
     */
    public function addMulti($items, $duration = 0)
    {
        $failedKeys = [];
        foreach ($items as $key => $value) {
            if ($this->add($key, $value, $duration) === false) {
                $failedKeys[] = $key;
            }
        }

        return $failedKeys;
    }

    /**
     * @DESC         |从缓存中删除具有指定键的值
     *
     * 参数区：
     *
     * @param $key
     * @return bool|mixed
     */
    public function delete($key)
    {
        $key       = $this->buildKey($key);
        $cacheFile = $this->cachePath . $key;
        // unlink用来删除文件
        return unlink($cacheFile);
    }

    /**。
     * @DESC         |从缓存中删除所有值。
     *                如果在多个应用程序之间执行共享缓存操作，请小心操作。
     *
     * 参数区：
     *
     * @return mixed|void
     */
    public function flush()
    {
        // 打开cache文件所在目录
        $dir = @dir($this->cachePath);

        // 列出目录中的所有文件
        while (($file = $dir->read()) !== false) {
            if ($file !== '.' && $file !== '..') {
                unlink($this->cachePath . $file);
            }
        }

        // 关闭目录
        $dir->close();
    }

    /**
     * @DESC         |从缓存中删除所有键的值。（清理缓存）
     *
     * 参数区：
     *
     * @return mixed|void
     */
    public function clear()
    {
        // 打开cache文件所在目录
        $dir = @dir($this->cachePath);

        // 列出目录中的所有文件
        while (($file = $dir->read()) !== false) {
            if ($file !== '.' && $file !== '..') {
                @file_put_contents($this->cachePath . $file, '', LOCK_EX);
            }
        }

        // 关闭目录
        $dir->close();
    }
}
