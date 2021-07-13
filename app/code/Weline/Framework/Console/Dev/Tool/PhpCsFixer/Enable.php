<?php
/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console\Dev\Tool\PhpCsFixer;


use Weline\Framework\App\Env;
use Weline\Framework\Output\Cli\Printing;

class Enable implements \Weline\Framework\Console\CommandInterface
{
    private Printing $printing;

    function __construct(
        Printing $printing
    )
    {
        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        Env::getInstance()->setConfig('php-cs',true);
        $this->printing->success(__('成功启用php-cs代码美化工具：'.Env::getInstance()->getConfig('php-cs')));
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '启用php-cs-fixer代码美化工具';
    }
}