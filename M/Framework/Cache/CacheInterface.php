<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/6
 * 时间：15:17
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Cache;


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