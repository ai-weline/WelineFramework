<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console\Console\Deploy\Mode;

use Weline\Framework\App\Env;
use Weline\Framework\App\System;
use Weline\Framework\Console\CommandAbstract;
use Weline\Framework\Console\Console\Deploy\Upgrade;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Console\Setup\Di\Compile;
use Weline\Framework\System\File\App\Scanner as AppScanner;
use Weline\Framework\View\Data\DataInterface;

class Set extends CommandAbstract
{
    /**
     * @var System
     */
    private System $system;

    public function __construct(
        System $system
    )
    {
        $this->system = $system;
    }

    public function execute(array $args = [], array $data = [])
    {
        array_shift($args);
        $param = array_shift($args);
        $this->printer->note('清理缓存...');
        /**@var $cacheManagerConsole \Weline\CacheManager\Console\Cache\Clear */
        $cacheManagerConsole = ObjectManager::getInstance(\Weline\CacheManager\Console\Cache\Clear::class);
        $cacheManagerConsole->execute();
        $this->printer->note('正在清除模组模板编译文件...');
        $this->cleanTplComDir();
        $this->clearGeneratedComplicateDir();
        switch ($param) {
            case 'prod':
                $this->printer->note('编译静态资源...');
                ObjectManager::getInstance(Compile::class)->execute();
                $this->printer->note('正在清除pub目录下生成的静态文件...');
                $this->cleanThemeDir();
                $this->printer->note('正在执行清理模板缓存...');
                $this->cleanTplComDir();
                $this->printer->note('正在执行静态资源部署...');
                /**@var $deploy_upgrade Upgrade */
                $deploy_upgrade = ObjectManager::getInstance(Upgrade::class);
                $deploy_upgrade->execute();
                break;
            case 'dev':
                $this->cleanTplComDir();
                $this->printer->note('正在执行清理模板缓存...');
                break;
            default:
                $this->printer->error(' ╮(๑•́ ₃•̀๑)╭  ：错误的部署模式：' . $param);
                $this->printer->note('(￢_￢) ->：允许的部署模式：dev/prod');

                return;
        }
        if (Env::getInstance()->setConfig('deploy', $param)) {
            $this->printer->success('（●´∀｀）♪ 当前部署模式：' . $param);
        } else {
            $this->printer->error('╮(๑•́ ₃•̀๑)╭ 部署模式设置错误：' . $param);
        }
    }

    public function tip(): string
    {
        return '部署模式设置。（dev:开发模式；prod:生产环境。）';
    }

    /**
     * @DESC         |清理模块编译目录
     *
     * 参数区：
     */
    protected function cleanTplComDir()
    {
        $modules = Env::getInstance()->getModuleList();
        foreach ($modules as $module) {
            $tpl_dir = $module['base_path'] . DS . 'view' . DS . 'tpl';
            if (is_dir($tpl_dir)) {
                $this->system->exec("rm -rf {$tpl_dir}");
            }
        }
    }

    public function clearGeneratedComplicateDir()
    {
        $complicate = Env::path_COMPLICATE_GENERATED_DIR;
        $this->system->exec("rm -rf $complicate");
    }

    /**
     * @DESC         |清理模块生成主题文件目录
     *
     * 参数区：
     *
     * @param string $theme
     *
     * @throws \Weline\Framework\App\Exception
     */
    protected function cleanThemeDir(string $theme = 'default')
    {
        $pub_theme_dir = PUB . 'static' . DS . $theme;
        if (is_dir($pub_theme_dir)) {
            $this->printer->warning('系统', $pub_theme_dir);
            $this->system->exec("rm -rf $pub_theme_dir");
        }
    }
}
