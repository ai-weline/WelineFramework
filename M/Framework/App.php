<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/10
 * 时间：20:32
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework;

use M\Framework\App\Helper;
use M\Framework\Router\Core as RouterCore;

class App
{
    private RouterCore $router;

    function __construct()
    {
        $this->router = Router\Core::getInstance();
    }

    function isCli()
    {
        return (PHP_SAPI === 'cli');
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
     *
     */
    function run()
    {
        if (!$this->isCli()) {
            $this->router->start();
        }
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return Helper
     */
    static function helper(): Helper
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