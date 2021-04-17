<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Register;

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
     * @param string $identity
     * @param string $type
     * @param array|string $param
     * @param string $version
     * @param string $description
     * @throws ConsoleException
     * @throws \Weline\Framework\App\Exception
     */
    public static function register(string $type, $param, string $version = '', string $description = '')
    {
        $install_params = [];
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
                if (!is_file($param . DIRECTORY_SEPARATOR . self::register_file)) {
                    throw new ConsoleException("{$moduleName}注册文件{$moduleRegisterFile}不存在！");
                }
                // 安装数据
                $install_params = [$moduleName, $version, $description];

                break;
            // 路由注册
            case self::ROUTER:
                // 安装数据
                $install_params = [$param];

                break;
            default:
        }
        /*
         * 采用观察者模式 是的其余类型的安装可自定义注册
         */
        /**@var DataObject $installerPathData */
        $installerPathData = ObjectManager::getInstance(DataObject::class);
        $installerPathData
            ->setData('installer', self::NAMESPACE . ucfirst($type) . '\Handle')
            ->setData('register_arguments', func_get_args());
        /**@var EventsManager $eventsManager */
        $eventsManager = ObjectManager::getInstance(EventsManager::class);
        $eventsManager->dispatch('Framework_Register::register_installer', ['data' => $installerPathData]);
        $installer_class = $installerPathData->getData('installer');

        // 注册 FIXME 修改注册handle
        try {
            /**@var RegisterInterface $installer */
            $installer = ObjectManager::getInstance($installer_class);
            if ($installer instanceof RegisterInterface) {
                $installer->register(...$installerPathData->getData('register_arguments'));
            } else {
                throw new ConsoleException($installer_class . __('安装器必须继承：') . RegisterInterface::class);
            }
        } catch (ConsoleException $exception) {
            throw $exception;
        }
    }
}
