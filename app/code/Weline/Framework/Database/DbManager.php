<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;

use Weline\Framework\Database\DbManager\ConfigProvider;
use Weline\Framework\Database\Exception\DbException;
use Weline\Framework\Database\Exception\LinkException;
use Weline\Framework\Manager\ObjectManager;

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
    protected ?LinkerFactory $defaultLinkerFactory = null;
    protected \WeakMap $linkers;
    protected ConfigProvider $configProvider;

    public function __construct(ConfigProvider $configProvider)
    {
        $this->linkers = new \WeakMap();
        $this->configProvider = $configProvider;
    }

    function __init(){
        $this->create();
    }

    /**
     * @DESC         |设置数据库配置
     *
     * 参数区：
     *
     * @param ConfigProvider $configProvider
     * @return $this
     */
    function setConfig(ConfigProvider $configProvider): static
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
     * @return LinkerFactory
     * @throws \ReflectionException
     * @throws LinkException|\Weline\Framework\App\Exception
     */
    function create(string $linker_name = 'default', ConfigProvider $configProvider = null): LinkerFactory
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
                $linker = ObjectManager::getInstance(LinkerFactory::class, [$configProvider]);
            }
        } else {
            if ($configProvider && empty($linker)) {
                $linker = ObjectManager::getInstance(LinkerFactory::class, [$configProvider]);
            } else {
                $linker = ObjectManager::getInstance(LinkerFactory::class);
            }
        }
        $this->linkers->offsetSet($linker, $linker_name);
        if('default'===$linker_name){
            $this->defaultLinkerFactory = $linker;
        }
        return $linker;
    }

    /**
     * @DESC         |获取连接
     *
     * 参数区：
     *
     * @param string $linker_name
     * @return LinkerFactory|null
     * @throws LinkException
     */
    function getLinker(string $linker_name = 'default'): ?LinkerFactory
    {
        if ('default' === $linker_name) {
            return $this->defaultLinkerFactory;
        }
        /**@var LinkerFactory $linker */
        foreach ($this->linkers->getIterator() as $linker => $linker_name_value) {
            if ($linker_name === $linker_name_value) {
                return $linker;
            }
        }
        throw new LinkException(__('链接异常：%1 链接不存在，或者尚未创建。',$linker_name));
    }

    /**
     * @DESC         |休眠时执行函数： 保存配置信息，以及模型数据
     *
     * 参数区：
     *
     * @return string[]
     */
    public function __sleep()
    {
        return array('configProvider');
    }

}
