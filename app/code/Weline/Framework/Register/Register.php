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
     * @param string $identity
     * @param string $type
     * @param array|string $param
     * @param string $version
     * @param string $description
     * @throws ConsoleException
     * @throws \Weline\Framework\App\Exception
     */
    public static function register(string $type, array|string $param, string $version = '', string $description = '')
    {
        $install_params = func_get_args();
        switch ($type) {
            // 模块安装
            case self::MODULE:
                $appPathArray = explode(DIRECTORY_SEPARATOR, $param);
                $module = array_pop($appPathArray);
                $vendor = array_pop($appPathArray);
                $code = array_pop($appPathArray);
                $app = array_pop($appPathArray);
                # 处理composer安装的模块名
                $moduleName = self::moduleName($vendor,$module);
                # 处理composer安装的模块
                $app .= DIRECTORY_SEPARATOR;
                if (is_int(strpos($param, VENDOR_PATH))) {
                    $app = '';
                }
                $module_path = $app . $code . DIRECTORY_SEPARATOR . $vendor . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR;
                $moduleRegisterFile = $module_path . self::register_file;
                if (!is_file($param . DIRECTORY_SEPARATOR . self::register_file)) {
                    throw new ConsoleException("{$moduleName}注册文件{$moduleRegisterFile}不存在！");
                }
                $module_dir_path = '';
                if (is_int(strpos($param, APP_CODE_PATH))) {
                    $module_dir_path = str_replace(APP_CODE_PATH, '', $param);
                }
                if (empty($module_dir_path) && is_int(strpos($param, VENDOR_PATH))) {
                    $module_dir_path = str_replace(VENDOR_PATH, '', $param);
                }

                // 安装数据
                $install_params = [$type, ['dir_path' => $module_dir_path, 'base_path' => $module_path, 'module_name' => $moduleName], $version, $description];

                break;
            // 路由注册
            case self::ROUTER:
                // 安装数据
                $install_params = [$type, $param];

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
            ->setData('register_arguments', $install_params);
        /**@var EventsManager $eventsManager */
        $eventsManager = ObjectManager::getInstance(EventsManager::class);
        $eventsManager->dispatch('Framework_return Register::register_installer', ['data' => $installerPathData]);
        $installer_class = $installerPathData->getData('installer');

        try {
            /**@var RegisterInterface $installer */
            $installer = ObjectManager::getInstance($installer_class);
            if ($installer instanceof RegisterInterface) {
                $register_arguments = $installerPathData->getData('register_arguments');
                unset($register_arguments[0]);// 去除type类型标志 因为后续的register继承自
                return $installer->register(...$register_arguments);
            } else {
                throw new ConsoleException($installer_class . __('安装器必须继承：') . RegisterInterface::class);
            }
        } catch (ConsoleException $exception) {
            throw $exception;
        }
    }

/*TODO 待搬迁至模组模块*/
    static function moduleName(string $vendor, string $module_name): string
    {
        $module_rename = '';
        if (is_int(strpos($module_name, '-'))) {
            $module_arr = explode('-', $module_name);
            foreach ($module_arr as $item) {
                $module_rename .= ucfirst($item);
            }
        }
        return ucfirst($vendor) . '_' . ucfirst($module_rename ?: $module_name);
    }
}
