<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Frontend\Model\Html;

use Weline\Backend\Model\Config;
use Weline\Framework\View\Data\HtmlInterface;

class Footer implements HtmlInterface
{
    const key = 'footer';
    const module = 'Weline_Frontend';
    private Config $backendConfig;

    function __construct(
        Config $backendConfig
    )
    {
        $this->backendConfig = $backendConfig;
    }

    /**
     * @DESC          # 返回Html头部配置
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/14 21:51
     * 参数区：
     * @return string
     */
    function getHtml(): string
    {
        return $this->backendConfig->getConfig(self::key, self::module);
    }

    /**
     * @DESC          # 设置头部Html代码
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/14 21:52
     * 参数区：
     * @param string $html
     */
    function setHtml(string $html)
    {
        $this->backendConfig->setConfig(self::key, $html, self::module);
    }
}