<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Module;

use Weline\Framework\Database\Model\ModelManager;
use Weline\Framework\Register\RegisterInterface;
use Weline\Framework\System\File\Compress;
use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\App\System;
use Weline\Framework\Console\ConsoleException;
use Weline\Framework\Helper\HandleInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Module\Helper\Data;
use Weline\Framework\Module\Must\DataInterface;
use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Setup\Helper\Data as SetupHelper;
use Weline\Framework\Setup\Data\Setup as SetupData;
use Weline\Framework\Setup\Data\Context as SetupContext;

class Handle implements HandleInterface, RegisterInterface
{
    const api_DIR = 'Api';// api特殊目录，注册api路由

    const pc_DIR = 'Controller';// pc特殊目录，注册pc路由

    private Printing $printer;

    private array $modules;

    /**
     * @var Data
     */
    private Data $helper;

    /**
     * @var System
     */
    private System $system;

    /**
     * @var SetupData
     */
    private SetupData $setup_data;

    /**
     * @var SetupHelper
     */
    private SetupHelper $setup_helper;

    /**
     * @var SetupContext
     */
    private SetupContext $setup_context;

    /**
     * @var Compress
     */
    private Compress $compress;

    /**
     * Handle 初始函数...
     * @param Data $helper
     * @param Printing $printer
     * @param System $system
     * @param SetupHelper $setup_helper
     * @param SetupData $setup_data
     * @param Compress $compress
     */
    public function __construct(
        Data        $helper,
        Printing    $printer,
        System      $system,
        SetupHelper $setup_helper,
        SetupData   $setup_data,
        Compress    $compress
    )
    {
        $this->modules = Env::getInstance()->getModuleList();
        $this->helper = $helper;
        $this->system = $system;
        $this->setup_data = $setup_data;
        $this->setup_helper = $setup_helper;
        $this->printer = $printer;
        $this->compress = $compress;
    }

    /**
     * @DESC         |移除应用
     *
     * 参数区：
     *
     * @param string $module_name
     * @throws \Weline\Framework\App\Exception
     */
    public function remove(string $module_name)
    {
        $app_path = APP_PATH;

        $this->printer->note(__('1、正在执行卸载脚本...'));
        $remove_script = $this->setup_helper->getSetupClass($module_name, \Weline\Framework\Setup\Data\DataInterface::type_REMOVE);
        if ($remove_script) {
            $remove_object = ObjectManager::getInstance($remove_script);

            $version = $this->modules[$module_name]['version'] ?? '1.0.0';
            $setup_context = new \Weline\Framework\Setup\Data\Context($module_name, $version);

            $this->printer->note($remove_object->setup($this->setup_data, $setup_context));
        } else {
            $this->printer->warning('模块卸载脚本不存在，已跳过卸载脚本！', '卸载');
        }
        $this->printer->note('2、备份应用程序...');
        if (is_dir($app_path . $this->modules[$module_name]['path'] . DIRECTORY_SEPARATOR)) {
            $back_path = $app_path . $this->modules[$module_name]['path'] . DIRECTORY_SEPARATOR;
        } elseif (is_dir($back_path = BP . 'vendor/' . $this->modules[$module_name]['path'] . DIRECTORY_SEPARATOR)) {
            $back_path = BP . 'vendor/' . $this->modules[$module_name]['path'] . DIRECTORY_SEPARATOR;
        } else {
            $this->printer->error("模块{$module_name}:不存在！", 'ERROR');
        }
        $module_path = $this->helper->getModulePath($module_name);
        $zip = $this->compress->compression("{$module_path}", APP_PATH . $module_name, APP_PATH);
        // TODO 完成模块卸载 兼容 win 和 linux

        $this->printer->note($zip);
        $this->printer->note('3、卸载应用代码...');

        $this->printer->note($back_path);
        $this->system->exec("rm $back_path -rf");
        $back_path = dirname($back_path);
        if ($this->system->getDirectoryObject()->is_empty(dirname($back_path))) {
            $this->system->exec("rm $back_path -rf");
        }
        $this->printer->success($module_name . __('模块已卸载完成！'));
    }

    /**
     * @DESC         |注册模块
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $name
     * @param string $version
     * @param string $description
     * @throws ConsoleException
     * @throws \Weline\Framework\App\Exception
     * @throws \ReflectionException
     */
    public function register(mixed $data, string $version = '', string $description = '')
    {
        if (!isset($data['base_path'])) {
            throw new Exception(__('尚未设置基础路径！%1', 'base_path'));
        }
        if (!isset($data['module_name'])) {
            throw new Exception(__('尚未设置模组名！%1', 'module_name'));
        }
        $name = $data['module_name'];
        if (DEV) $this->printer->error($name . '：处理...', '开发');
        // 模块路径
        $module_path = $data['base_path'];
        // 模型管理器
        /**@var ModelManager $modelManager */
        $modelManager = ObjectManager::getInstance(ModelManager::class);
        // 检测文件完整
        $router = '';
        foreach (DataInterface::files as $filename) {
            $filepath = BP.$module_path . $filename;
            if (is_file($filepath)) {
                if ($filename === DataInterface::file_etc_Env) {
                    $env = (array)require $filepath;
                    if (!isset($env['router'])) {
                        // 如果文件不存在则读取模块名字作为router
                        $env['router'] = strtolower($name);
                        if (DEV) {
                            $this->printer->note($name . '：模块没有设定路由别名，因此沿用模块名称作为路由入口！', '开发');
                            $this->printer->warning('{http://demo.com/' . $name . '}', '示例');
                            $this->printer->warning('设置路由别名请到：模块目录下的etc/env.php,修改return ["router"=>"' . $name . '"];', '提示');
                        }
                    }
                    $router = strtolower($env['router']);// TODO 定义路由区分大小写
                }
            }
        }
//        if($name === 'Aiweline_WebsiteMonitoring'){
//            p($router);
//        }
        $this->setup_context = ObjectManager::make(SetupContext::class, ['module_name' => $name, 'module_version' => $version, 'module_description' => $description], '__construct');
        $setup_dir = BP . $module_path . \Weline\Framework\Setup\Data\DataInterface::dir;
        if (is_dir($setup_dir) && DEV) $this->printer->setup($setup_dir . '：升级目录...', '开发');
        // 已经存在模块则更新
        if ($this->helper->isInstalled($this->modules, $name)) {
            if ($this->helper->isDisabled($this->modules, $name)) {
                echo $this->printer->warning(str_pad($name, 45) . __('已禁用！'));
                return;
            }
            // 是否更新模块：是则加载模块下的Setup模块下的文件进行更新
            $old_version = $this->modules[$name]['version'];
            if ($this->helper->isUpgrade($this->modules, $name, $version)) {
                $this->printer->note(__("扩展 %1 升级中...", $name));
                $this->printer->setup(__('升级 %1 到 %2', [$old_version, $version]));

                # 升级模块的模型
                $modelManager->update($name, $this->setup_context, 'upgrade');

                foreach (\Weline\Framework\Setup\Data\DataInterface::upgrade_FILES as $upgrade_FILE) {
                    $setup_file = $setup_dir . DIRECTORY_SEPARATOR . $upgrade_FILE . '.php';
                    if (file_exists($setup_file)) {
                        // 获取命名空间
                        $setup_file_arr = explode(APP_PATH, $setup_file);
                        $file_namespace = rtrim(str_replace(DIRECTORY_SEPARATOR, '\\', array_pop($setup_file_arr)), '.php');
                        $setup = ObjectManager::getInstance($file_namespace);
                        $result = $setup->setup($this->setup_data, $this->setup_context);
                        $this->printer->note("{$result}");
                    }
                }
                $this->modules[$name]['version'] = $version ?: '1.0.0';
                $this->modules[$name]['description'] = $description ?: '';
                $this->modules[$name]['base_path'] = $module_path;
                // 更新模块
                $this->helper->updateModules($this->modules);
            }

            # 升级模块的模型
            if (DEV) $this->printer->setup($name . '：模型升级...', '开发');
            if (DEV) $modelManager->update($name, $this->setup_context, 'setup');
            if (DEV) $this->printer->setup($name . '：模型升级完成...', '开发');
            // 更新路由
            if (DEV) $this->printer->setup($name . '：更新路由...', '开发');
            $this->helper->registerModuleRouter($this->modules, $module_path, $name, $router);
            if (DEV) $this->printer->setup($name . '：更新路由完成...', '开发');
            // 更新模块
            $this->modules[$name]['base_path'] = $module_path;
            $this->helper->updateModules($this->modules);
            echo $this->printer->success(str_pad($name, 45) . __('已更新！'));
        } else {
            $this->printer->note("扩展{$name}安装中...");
            # 模型安装install
            $modelManager->update($name, $this->setup_context, 'install');
            // 全新安装
            $moduleData = [
                'status' => 1,
                'version' => $version ? $version : '1.0.0',
                'router' => $router,
                'description' => $description ? $description : '',
                'path' => $this->helper->moduleNameToPath($this->modules, $name),
                'base_path' => $module_path,
            ];
            $this->modules[$name] = $moduleData;

            try {
                // 安装模块：加载模块下的Setup模块下的安装文件进行安装
                foreach (\Weline\Framework\Setup\Data\DataInterface::install_FILES as $install_FILE) {
                    $setup_file = $setup_dir . DIRECTORY_SEPARATOR . $install_FILE . '.php';
                    if (file_exists($setup_file)) {
                        // 获取命名空间
                        $setup_file_arr = explode(APP_PATH, $setup_file);
                        $file_namespace = rtrim(str_replace(DIRECTORY_SEPARATOR, '\\', array_pop($setup_file_arr)), '.php');
                        $setup = ObjectManager::getInstance($file_namespace);
                        $setup->setup($this->setup_data, $this->setup_context);
                    }
                    $this->printer->success(str_pad($name, 45) . __('已安装！'));
                }
            } catch (Exception $exception) {
                throw $exception;
            }

            # 执行模型setup
            if (DEV) $modelManager->update($name, $this->setup_context, 'setup');

            // 更新模块
            $this->helper->updateModules($this->modules);

            // 更新路由
            $this->helper->registerModuleRouter($this->modules, $module_path, $name, $router);
        }
    }
}
