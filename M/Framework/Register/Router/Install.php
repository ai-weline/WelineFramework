<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/20
 * 时间：23:34
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Register\Router;


use M\Framework\Console\ConsoleException;
use M\Framework\Register\Router\Data\DataInterface;
use M\Framework\Router\Handle;

class Install implements DataInterface
{
    /**
     * @DESC         |路由安装
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array $routerParam
     * @throws \M\Framework\Console\ConsoleException
     * @throws \M\Framework\App\Exception
     */
    static function install(array $routerParam)
    {
        if (count(array_intersect_key(self::register_param, $routerParam)) != count(self::register_param)) {
            $params = '';
            foreach (self::register_param as $key => $item) {
                $params .= $key . ',';
            }
            throw new ConsoleException('路由注册所需参数不完整：' . rtrim($params,','));
        }
        // 注册
        $moduleHandler = new Handle();
        $moduleHandler->register($routerParam);
    }
}