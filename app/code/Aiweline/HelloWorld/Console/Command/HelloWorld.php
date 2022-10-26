<?php

declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/5/13
 * 时间：16:39
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\HelloWorld\Console\Command;

use Weline\Framework\Output\Cli\Printing;

class HelloWorld implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @var Printing
     */
    private Printing $printing;

    public function __construct(
        Printing $printing
    ) {
        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $args = [], array $data = [])
    {
        $this->printing->printing(__('欢迎来到命令交互世界！'));
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return __('欢迎来到命令交互世界！');
    }
}
