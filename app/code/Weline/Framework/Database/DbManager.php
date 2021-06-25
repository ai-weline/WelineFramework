<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;

use JetBrains\PhpStorm\Pure;
use PDO;
use WeakMap;
use Weline\Framework\Database\DbManager\ConfigProvider;
use Weline\Framework\Manager\Cache\ObjectCache;
use Weline\Theme\Model\WelineTheme;

/**
 * 文件信息
 * DESC:   |
 * 作者：   秋枫雁飞
 * 日期：   2020/7/2
 * 时间：   1:24
 * 网站：   https://bbs.aiweline.com
 * Email：  aiweline@qq.com
 */
class DbManager
{
    protected ?Linker $defaultLinker = null;
    protected WeakMap $linkers;
    protected ConfigProvider $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->linkers = new WeakMap();
        $this->configProvider = $configProvider;
    }

    /**
     * @DESC         |设置数据库配置
     *
     * 参数区：
     *
     * @param ConfigProvider $configProvider
     * @return $this
     */
    function setConfig(ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
        return $this;
    }

    /**
     * @DESC         |数据库配置
     *
     * 参数区：
     *
     * @return ConfigProvider
     */
    function getConfig(): ConfigProvider
    {
        return $this->configProvider;
    }

    /**
     * @DESC         |创建链接资源
     *
     * 兼并新链接
     *
     * 参数区：
     * @param string $linker_name 链接名称
     * @param ConfigProvider|null $configProvider 链接资源配置
     * @return Linker
     */
    function create(string $linker_name = 'default', ConfigProvider $configProvider = null)
    {
        $linker = $this->getLinker($linker_name);
        // 如果不更新连接配置，且已经存在连接就直接读取
        if (empty($configProvider) && $linker) {
            return $linker;
        }
        // 存在连接配置则
        if ($configProvider && $linker) {
            // 如果更新连接配置，但是配置内容一致，且存在使用此配置存在的连接则直接返回
            if ($linker->getConfigProvider()->getData() == $configProvider->getData()) {
                return $linker;
            } else {
                $linker = new Linker($configProvider);
            }
        } else {
            $linker = new Linker($this->configProvider);
        }
        $this->linkers->offsetSet($linker, $linker_name);
        return $linker;
    }

    /**
     * @DESC         |获取连接
     *
     * 参数区：
     *
     * @param string $linker_name
     * @return Linker|null
     */
    function getLinker($linker_name = 'default')
    {
        if ('default' === $linker_name) {
            return $this->defaultLinker;
        }
        /**@var Linker $linker */
        foreach ($this->linkers->getIterator() as $linker => $linker_name_value) {
            if ($linker_name === $linker_name_value) {
                return $linker;
            }
        }
        return null;
    }
}
