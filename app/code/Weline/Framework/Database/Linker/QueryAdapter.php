<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/6/21
 * 时间：11:45
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Database\Linker;


use Weline\Framework\Database\DbManager\ConfigProvider;
use Weline\Framework\Database\Exception\LinkException;
use Weline\Framework\Database\Model;

class QueryAdapter
{
    private ?ConfigProvider $configProvider = null;

    /**
     * @throws \Weline\Framework\App\Exception
     * @throws LinkException
     */
    function create(): QueryInterface
    {
        $adapter_class = 'Weline\\Framework\\Database\\Linker\\Query\\'.ucfirst($this->getConfigProvider()->getDbType());
        return new $adapter_class();
    }

    /**
     * @DESC         |设置配置提供器
     *
     * 参数区：
     *
     * @param ConfigProvider $configProvider
     * @return QueryAdapter
     */
    function setConfigProvider(ConfigProvider $configProvider): QueryAdapter
    {
        $this->configProvider = $configProvider;
        return $this;
    }

    /**
     * @DESC         |获取方法提供器
     *
     * 参数区：
     *
     * @return ConfigProvider
     * @throws LinkException
     * @throws \Weline\Framework\App\Exception
     */
    function getConfigProvider(): ConfigProvider
    {
        if (is_null($this->configProvider)) {
            throw new LinkException(__('尚未设置数据库配置提供器！'));
        }
        return $this->configProvider;
    }
}