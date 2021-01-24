<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Cache\Console\Cache;

use Weline\Framework\Cache\Scanner;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Cli\Printing;

class Clear implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @var Scanner
     */
    private Scanner $scanner;
    /**
     * @var Printing
     */
    private Printing $printing;

    public function __construct(
        Scanner $scanner,
        Printing $printing
    )
    {
        $this->scanner = $scanner;
        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        $caches = $this->scanner->getCaches();
        foreach ($caches as $form => $cache) {
            switch ($form) {
                case 'app_caches':
                    $this->printing->note(__('模块缓存清理中...'));
                    foreach ($cache as $app_cache) {
                        $this->printing->printing(__($app_cache['class'].'...'));
                        ObjectManager::getInstance($app_cache['class'])->create()->clear();
                    }
                    break;
                case 'framework_caches':
                    $this->printing->note(__('框架缓存清理中...'));
                    foreach ($cache as $framework_cache) {
                        $this->printing->printing(__($framework_cache['class'].'...'));
                        ObjectManager::getInstance($framework_cache['class'])->create()->clear();
                    }
                    break;
                default:
                    $this->printing->error(__('没有任何类型的缓存需要清理！'));
            }
        }
        $this->printing->success(__('缓存已清理！'));
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '缓存清理。';
    }
}
