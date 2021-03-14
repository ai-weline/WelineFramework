<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework;

use Weline\Framework\App\Env;
use Weline\Framework\App\Helper;
use Weline\Framework\Router\Core as RouterCore;

class App
{
    /**
     * @var RouterCore
     */
    private RouterCore $router;

    public function __construct(
        RouterCore $router
    ) {
        $this->router = $router;
    }

    /**
     * @DESC         |初始化
     *
     * 参数区：
     */
    public static function init()
    {
        // 执行时间
        define('START_TIME', microtime(true));
        // 系统是否WIN
        define('IS_WIN', strtolower(substr(PHP_OS, 0, 3)) === 'win');
        // 导入核心通用组件
        require __DIR__ . '/Common/loader.php';
        // 助手函数
        require BP . 'app' . DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'functions.php';
        /**------------环境配置----------------*/
        $env = Env::getInstance();
        // 调试模式
        define('DEV', $env->getConfig('deploy', false) === 'dev');
        // 代码标准化模式
        define('PHP_CS', $env->getConfig('php-cs', false));
        //报告错误
        DEV ? error_reporting(E_ALL) : error_reporting(0);
        // 检查运行模式
        defined('CLI') ?: define('CLI', PHP_SAPI === 'cli');
    }

    /**
     * @DESC         |框架应用运行
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     */
    public function run()
    {
        if (! CLI) {
            $this->router->start();
        }
    }

    /**
     * @DESC         |安装
     *
     * 参数区：
     */
    public function install()
    {
        require BP . 'setup/index.php';
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return Helper
     */
    public static function helper(): Helper
    {
        return new App\Helper();
    }

    /**
     * @return RouterCore
     */
    public function getRouter(): RouterCore
    {
        return $this->router;
    }
}
