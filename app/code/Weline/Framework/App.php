<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework;

use SebastianBergmann\CodeCoverage\StaticAnalysis\CacheWarmer;
use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\App\Helper;
use Weline\Framework\Cache\CacheFactory;
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
     * @return mixed
     */
    public static function Env(string $key = null, $value = null): mixed
    {
        if (!isset(self::$_env)) {
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
     * @throws Exception
     */
    public static function run(): string
    {
        self::init();
        if (!CLI) {
            try {
                return ObjectManager::getInstance(\Weline\Framework\Router\Core::class)->start();
            } catch (\ReflectionException | App\Exception $e) {
                throw new Exception(__('系统错误：%1', $e->getMessage()));
            }
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
