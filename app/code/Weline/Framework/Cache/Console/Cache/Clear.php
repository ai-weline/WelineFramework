<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/1/24
 * 时间：18:33
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Cache\Console\Cache;


use Weline\Framework\App\Env;

class Clear implements \Weline\Framework\Console\CommandInterface
{

    /**
     * @var Env
     */
    private Env $env;

    function __construct(
        Env $env
    )
    {
        $this->env = $env;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {

    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '缓存清理。';
    }
}