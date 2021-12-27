<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Module\Console\Module;

use Weline\Framework\App\Env;
use Weline\Framework\Output\Cli\Printing;

class Listing implements \Weline\Framework\Console\CommandInterface
{
    private Printing $printing;

    function __construct(
        Printing $printing
    ){

        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        foreach (Env::getInstance()->getModuleList() as $name=>$module) {
            $this->printing->note($name);
        }
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '查看模块列表';
    }
}