<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/11/26 15:02:25
 */

namespace Weline\Framework\Setup\Console\Setup;

use Weline\Framework\App\Env;
use Weline\Framework\App\System;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Module\Helper\Data;
use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\System\File\App\Scanner as AppScanner;

class Upgrade implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @var System
     */
    private System $system;

    /**
     * @var AppScanner
     */
    private AppScanner $scanner;

    /**
     * @var Data
     */
    private Data $data;

    public function __construct(
        Printing   $printer,
        AppScanner $scanner,
        Data       $data,
        System     $system
    )
    {
        $this->printer = $printer;
        $this->system  = $system;
        $this->scanner = $scanner;
        $this->data    = $data;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $args = [], array $data = [])
    {
        $i = 1;
        // 删除路由文件
        $this->printer->warning($i . '、路由更新...', '系统');
        $this->printer->warning('清除文件：');
        foreach (Env::router_files_PATH as $path) {
            $this->printer->warning($path);
            if (is_file($path)) {
                $data = $this->system->exec('rm -f ' . $path);
                if ($data) {
                    $this->printer->printList($data);
                }
            }
        }
        $i += 1;
        $this->printer->warning($i . '、generated生成目录代码code清理...', '系统');
        $this->system->exec('rm -rf ' . Env::path_framework_generated_code);
        $i += 1;
        // 扫描代码
        $this->printer->note($i . '、清理模板缓存', '系统');
        $modules = Env::getInstance()->getModuleList();
        foreach ($modules as $module) {
            $tpl_dir = $module['base_path'] . DS . 'view' . DS . 'tpl';
            if (is_dir($tpl_dir)) {
                $this->system->exec("rm -rf {$tpl_dir}");
            }
        }
        $i += 1;
        $this->printer->note($i . '、module模块更新...');
        // 注册模块
        $all_modules = [];
        // 扫描代码
        list($vendor, $dependencies) = $this->scanner->scanAppModules();
        foreach ($dependencies as $module_name => $module) {
            $register = $module['register'] ?? '';
            if (is_file($register)) {
                require $register;
            }
        }
        $this->printer->note('模块更新完毕！');
        $i += 1;
        $this->printer->note($i . '、收集模块信息', '系统');
        # 加载module中的助手函数
        $modules                = Env::getInstance()->getActiveModules();
        $function_files_content = '';
        foreach ($modules as $module) {
            $global_file_pattern = $module['base_path'] . 'Global' . DS . '*.php';
            $global_files        = glob($global_file_pattern);
            foreach ($global_files as $global_file) {
                # 读取文件内容 去除注释以及每个文件末尾的 '\?\>'结束符
                $function_files_content .= str_replace('?>', '', file_get_contents($global_file)) . PHP_EOL;
            }
        }
        # 写入文件
        $this->printer->warning('写入文件：');
        $this->printer->warning(Env::path_FUNCTIONS_FILE);
        file_put_contents(Env::path_FUNCTIONS_FILE, $function_files_content);

        $i += 1;
        $this->printer->note($i . '、事件清理...');
        /**@var $cacheManagerConsole \Weline\CacheManager\Console\Cache\Clear */
        $cacheManagerConsole = ObjectManager::getInstance(\Weline\Framework\Event\Console\Event\Cache\Clear::class);
        $cacheManagerConsole->execute();
        $i += 1;
        $this->printer->note($i . '、插件编译...');
        /**@var $cacheManagerConsole \Weline\CacheManager\Console\Cache\Clear */
        $cacheManagerConsole = ObjectManager::getInstance(\Weline\Framework\Plugin\Console\Plugin\Di\Compile::class);
        $cacheManagerConsole->execute();
        $i += 1;

        /**@var EventsManager $eventsManager */
        $eventsManager = ObjectManager::getInstance(EventsManager::class);
        $eventsManager->dispatch('Framework_Module::module_upgrade');

        // 清理其他
        $this->printer->note($i . '、清理缓存...');
        /**@var $cacheManagerConsole \Weline\CacheManager\Console\Cache\Flush */
        $cacheManagerConsole = ObjectManager::getInstance(\Weline\CacheManager\Console\Cache\Flush::class);
        $cacheManagerConsole->execute();
        $this->system->exec('rm -rf ' . BP . 'var' . DS . 'cache');
    }

    /**
     * @inheritDoc
     */
    public function tip(): string
    {
        return '框架代码刷新。';
    }
}
