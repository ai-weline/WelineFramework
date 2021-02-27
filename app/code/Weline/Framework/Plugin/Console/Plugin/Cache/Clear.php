<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/2/17
 * 时间：20:38
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Plugin\Console\Plugin\Cache;


use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Plugin\Cache\PluginCache;

class Clear implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @var CacheInterface
     */
    private CacheInterface $pluginCache;
    /**
     * @var Printing
     */
    private Printing $printing;

    function __construct(
        PluginCache $pluginCache,
        Printing $printing
    )
    {
        $this->pluginCache = $pluginCache->create();
        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        $this->pluginCache->clear();
        $this->printing->success(__('拦截器缓存清理成功！'),'系统');
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return __('插件缓存清理！');
    }
}