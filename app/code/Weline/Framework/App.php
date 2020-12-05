<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework;

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

    public function __init()
    {
        // 检查运行模式
        defined('CLI') ?: define('CLI', PHP_SAPI === 'cli');
        // 导入核心通用组件
        require __DIR__ . '/Common/loader.php';
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
