<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/20
 * 时间：13:32
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Register;


use M\Framework\Console\ConsoleException;

class Register implements RegisterInterface
{

    /**
     * @DESC         |注册
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $type
     * @param array|string $param
     * @param string $version
     * @param string $description
     * @return mixed
     * @throws ConsoleException
     */
    static function register(string $type, $param, string $version = '', string $description = '')
    {
        $installerPath = self::NAMESPACE . ucfirst($type) . '\Install';
        try {
            $installer = new $installerPath();
        } catch (ConsoleException $exception) {
            throw new ConsoleException(__('不存在的安装类型：') . $type);
        }
        switch ($type) {
            // 模块安装
            case self::MODULE:
                $appPathArray = explode(DIRECTORY_SEPARATOR, $param);
                $module = array_pop($appPathArray);
                $vendor = array_pop($appPathArray);
                $code = array_pop($appPathArray);
                $app = array_pop($appPathArray);
                $moduleName = $vendor . '_' . $module;

                $moduleRegisterFile = $app . DIRECTORY_SEPARATOR . $code . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . self::register_file;
                if (!is_file($param . DIRECTORY_SEPARATOR . self::register_file))
                    throw new ConsoleException("{$moduleName}注册文件{$moduleRegisterFile}不存在！");
                // 安装数据
                return $installer::install($moduleName, $version, $description);
                break;

            // 路由注册
            case self::ROUTER:
                // 安装数据
                return $installer::install($param);
                break;
            default:
                throw new ConsoleException('暂不支持此安装类型：' . $type);
        }

    }
}