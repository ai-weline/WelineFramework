<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache\Driver;

use Weline\Framework\App\Env;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\DataObject\DataObject;

abstract class CacheDriverAbstract implements \Weline\Framework\Cache\CacheDriverInterface
{
    protected bool $status;
    protected array $config;
    protected string $identity;
    protected string $tip;

    public function __construct(string $identity, array $config, $tip = '')
    {
        $this->identity = $identity;
        $this->config   = $config;
        $this->tip      = $tip;
        $this->status   = (bool)Env::getInstance()->getData('cache_status/' . $identity);
        if (method_exists($this, '__init')) {
            $this->__init();
        }
    }

    function getIdentify(): string
    {
        return $this->identity;
    }

    function getStatus(): bool
    {
        return $this->status;
    }

    function tip(): string
    {
        return $this->tip;
    }
}