<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Register;

use Weline\Framework\App;
use Weline\Framework\Console\ConsoleException;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;

class Register implements RegisterDataInterface
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
     * @param string       $type        注册类型
     * @param string       $module_name 模组名
     * @param array|string $param       参数[模组类型:此处传输目录__DIR__,主题类型：['name' => 'demo','path' => __DIR__,]]
     * @param string       $version     版本
     * @param string       $description 描述
     *
     * @return mixed
     */
    public static function register(string $type, string $module_name, array|string $param, string $version = '', string $description = ''): mixed
    {
        $install_params = func_get_args();
        switch ($type) {
            // 模块安装
            case self::MODULE:
                $appPathArray       = explode(DIRECTORY_SEPARATOR, $param);
                $module_name_dir    = array_pop($appPathArray);
                $vendor_dir         = array_pop($appPathArray);
                $moduleRegisterFile = $param . DIRECTORY_SEPARATOR . self::register_file;
                if (!is_dir($param)) {
                    return '';
                }
                if (!is_file($moduleRegisterFile)) {
                    throw new ConsoleException("{$module_name}注册文件{$moduleRegisterFile}不存在！");
                }
                // 安装数据
                $install_params = [$type, $module_name, ['dir_path' => $vendor_dir . DIRECTORY_SEPARATOR . $module_name_dir . DIRECTORY_SEPARATOR, 'base_path' => $param . DIRECTORY_SEPARATOR, 'module_name' => $module_name], $version, $description];
                break;
            // 路由注册
            case self::ROUTER:
            default:
        }
        /*
         * 采用观察者模式 是的其余类型的安装可自定义注册
         */
        /**@var DataObject $installerPathData */
        $installerPathData = ObjectManager::getInstance(DataObject::class);
        $installerPathData
            ->setData('installer', self::NAMESPACE . ucfirst($type) . '\Handle')
            ->setData('register_arguments', $install_params);
        /**@var EventsManager $eventsManager */
        $eventsManager = ObjectManager::getInstance(EventsManager::class);
        $eventsManager->dispatch('Framework_Register::register_installer', ['data' => $installerPathData]);
        $installer_class = $installerPathData->getData('installer');
        /**@var RegisterInterface $installer */
        $installer = ObjectManager::getInstance($installer_class);
        if ($installer instanceof RegisterInterface) {
            $register_arguments = $installerPathData->getData('register_arguments');
            return $installer->register(...$register_arguments);
        } else {
            throw new ConsoleException($installer_class . __('安装器必须继承：') . RegisterInterface::class);
        }
    }

    public static function moduleName(string $vendor, string $module_name): string
    {
        return self::parserModuleVendor($vendor) . '_' . self::parserModuleName($module_name);
    }

    public static function parserModuleVendor(string $vendor): string
    {
        return ucfirst($vendor);
    }

    public static function parserModuleName(string $module_name): string
    {
        $module_rename = '';
        if (is_int(strpos($module_name, '-'))) {
            $module_arr = explode('-', $module_name);
            foreach ($module_arr as $item) {
                $module_rename .= ucfirst($item);
            }
        } else {
            $module_rename = $module_name;
        }
        return ucfirst($module_rename);
    }
}
