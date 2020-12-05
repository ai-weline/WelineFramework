<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache;

interface CacheInterface
{
    /**
     * @DESC         |从给定键生成规范化缓存键。
     *
     * 参数区：
     *
     * @param $key
     * @return mixed
     */
    public function buildKey($key);

    /**
     * @DESC         |使用指定键从缓存中检索值。
     *
     * 参数区：
     *
     * @param $key
     * @return mixed
     */
    public function get($key);

    /**
     * @DESC         |检查缓存中是否存在指定的键。
     *
     * 参数区：
     *
     * @param $key
     * @return mixed
     */
    public function exists($key);

    /**
     * @DESC         |使用指定的键从缓存中检索多个值。
     *
     * 参数区：
     *
     * @param $keys
     * @return mixed
     */
    public function getMulti($keys);

    /**
     * @DESC         |将键标识的值存储到缓存中。
     *
     * 参数区：
     *
     * @param $key
     * @param $value
     * @param int $duration
     * @return mixed
     */
    public function set($key, $value, int $duration = 0);

    /**
     * @DESC         |在缓存中存储多个项目。每个项包含一个由键标识的值。
     *
     * 参数区：
     *
     * @param $items
     * @param int $duration
     * @return mixed
     */
    public function setMulti($items, int $duration = 0);

    /**
     * @DESC         |如果缓存不包含该键，则将由键标识的值存储到缓存中。
     *                如果缓存已包含密钥，则不会执行任何操作。
     *
     * 参数区：
     *
     * @param $key
     * @param $value
     * @param int $duration
     * @return mixed
     */
    public function add($key, $value, int $duration = 0);

    /**
     * @DESC         |在缓存中存储多个项目。每个项包含一个由键标识的值。
     *                如果缓存已经包含这样一个键，则现有值和过期时间将被保留。
     *
     * 参数区：
     *
     * @param $items
     * @param int $duration
     * @return mixed
     */
    public function addMulti($items, int $duration = 0);

    /**
     * @DESC         |从缓存中删除具有指定键的值
     *
     * 参数区：
     *
     * @param $key
     * @return mixed
     */
    public function delete($key);

    /**
     * @DESC         |从缓存中删除所有值。（刷新缓存文件）
     *
     * 参数区：
     *
     * @return mixed
     */
    public function flush();

    /**
     * @DESC         |从缓存中删除所有键的值。（清理缓存）
     *
     * 参数区：
     *
     * @return mixed
     */
    public function clear();
}
