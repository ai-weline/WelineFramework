<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework;

use Weline\Framework\App\Env;
use Weline\Framework\App\Helper;
use Weline\Framework\Manager\ObjectManager;

class App
{
    /**
     * @var Env
     */
    private static Env $_env;

    /**
     * @DESC         |环境变量操作
     *
     * 参数区：
     *
     * @param string|null $key
     * @param null $value
     * @return array|bool|mixed|Env|null
     */
    public static function Env(string $key = null, $value = null)
    {
        if (! isset(self::$_env)) {
            self::$_env = Env::getInstance();
        }
        if ($key && empty($value)) {
            return self::$_env->getConfig($key);
        }
        if ($key && $value) {
            return self::$_env->setConfig($key, $value);
        }

        return self::$_env;
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
        // 调试模式
        define('DEV', self::Env('deploy') === 'dev');
        //报告错误
        DEV ? error_reporting(E_ALL) : error_reporting(0);
        // 检查运行模式
        defined('CLI') ?: define('CLI', PHP_SAPI === 'cli');

        // 错误报告
        if (DEV || CLI) {
            ini_set('error_reporting', E_ALL);
            register_shutdown_function(function () {
                $_error = error_get_last();
                if ($_error && in_array($_error['type'], [1, 4, 16, 64, 256, 4096, E_ALL], true)) {
                    if (CLI) {
                        echo __('致命错误：') . PHP_EOL;
                        echo __('文件：') . $_error['file'] . PHP_EOL;
                        echo __('行数：') . $_error['line'] . PHP_EOL;
                        echo __('消息：') . $_error['message'] . PHP_EOL;
                    } else {
                        echo '<b style="color: red">致命错误：</b></br>';
                        echo '<pre>';
                        echo __('文件：') . $_error['file'] . '</br>';
                        echo __('行数：') . $_error['line'] . '</br>';
                        echo __('消息：') . $_error['message'] . '</br>';
                        echo '</pre>';
                    }
                }
            });
        }
//        else{
//            ini_set('error_reporting', 0);
//        }
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
    public static function run()
    {
        self::init();
        if (! CLI) {
            return ObjectManager::getInstance(\Weline\Framework\Router\Core::class)->start();
        }

        return '';
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
}
