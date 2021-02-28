<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin\Console\Plugin\Di;

use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Plugin\PluginsManager;
use Weline\Framework\Plugin\Proxy\Generator;

class Compile implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @var PluginsManager
     */
    private PluginsManager $pluginsManager;
    /**
     * @var Printing
     */
    private Printing $printing;

    public function __construct(
        PluginsManager $pluginsManager,
        Printing $printing
    )
    {
        $this->pluginsManager = $pluginsManager;
        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        $this->printing->printing(__('编译开始...'));
        $generator = $this->pluginsManager->generatorInterceptor('',false);
        $printer_list = [];
        foreach ($generator::getClassProxyMap() as $key=>$item) {
            unset($item['body']);
            $printer_list[$key]=$item;
        }
        $this->printing->printList($printer_list);
        $this->printing->printing(__('编译结束...'));
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '系统依赖编译';
    }
}
