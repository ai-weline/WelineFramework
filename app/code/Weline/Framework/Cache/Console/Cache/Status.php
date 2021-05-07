<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/5/7
 * 时间：17:54
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Cache\Console\Cache;


use Weline\Framework\Cache\Scanner;

class Status implements \Weline\Framework\Console\CommandInterface
{

    /**
     * @var Scanner
     */
    private Scanner $scanner;

    function __construct(
        Scanner $scanner
    )
    {
        $this->scanner = $scanner;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        $caches = $this->scanner->scanAppCaches();
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        // TODO: Implement getTip() method.
    }
}