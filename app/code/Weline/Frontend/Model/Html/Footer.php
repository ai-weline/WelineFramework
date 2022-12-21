<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Frontend\Model\Html;

use Weline\Frontend\Model\Config;
use Weline\Framework\View\Data\HtmlInterface;

class Footer implements HtmlInterface
{
    public const key    = 'footer';
    public const module = 'Weline_Frontend';
    private Config $frontendConfig;
    private string $_html = '';

    public function __construct(
        Config $frontendConfig
    )
    {
        $this->frontendConfig = $frontendConfig;
    }

    /**
     * @DESC          # 返回Html头部配置
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/14 21:51
     * 参数区：
     * @return string
     */
    public function getHtml(): string
    {
        return ($this->frontendConfig->getConfig(self::key, self::module) ?? '') . $this->_html;
    }

    /**
     * @DESC          # 设置头部Html代码
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/14 21:52
     * 参数区：
     *
     * @param string $html
     *
     * @return Footer
     */
    public function setHtml(string $html): static
    {
        $this->_html = $html;
        $this->frontendConfig->setConfig(self::key, $html, self::module);
        return $this;
    }

    public function addHtml(string $html): static
    {
        $this->_html .= $html;
        return $this;
    }
}
