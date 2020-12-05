<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Module;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Console\ConsoleException;
use Weline\Framework\Helper\AbstractHelper;
use Weline\Framework\Helper\HandleInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Module\Helper\Data;
use Weline\Framework\Module\Must\DataInterface;
use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Setup\Helper\Data as SetupHelper;

class Handle implements HandleInterface
{
    private \Weline\Framework\Setup\Data\Setup $setup_tool;

    private \Weline\Framework\Setup\Data\Context $setup_context;

    const api_DIR = 'Api';// api特殊目录，注册api路由

    const pc_DIR = 'Controller';// pc特殊目录，注册pc路由

    private Printing $printer;

    private array $modules;

    private SetupHelper $setup_helper;

    /**
     * @var Data
     */
    private Data $helper;

    public function __construct(
        Data $helper,
        Printing $printing
    ) {
        $this->modules = Env::getInstance()->getModuleList();
        $this->printer = $printing;
        $this->helper  = $helper;
    }

    public function getHelper(): AbstractHelper
    {
        return $this->helper;
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
        $module_list = Env::getInstance()->getModuleList();
        if (! isset($this->setup_tool)) {
            $this->setup_tool = new \Weline\Framework\Setup\Data\Setup();
        }
        if (! isset($this->setup_helper)) {
            $this->setup_helper = new SetupHelper();
        }
        $app_path = APP_PATH;
        $this->printer->note(__('1、正在执行卸载脚本...'));
        $remove_script = $this->setup_helper->getSetupClass($module_name, \Weline\Framework\Setup\Data\DataInterface::type_REMOVE);
        if ($remove_script) {
            $remove_object = new $remove_script();

            $version       = $module_list[$module_name]['version'] ?? '1.0.0';
            $setup_context = new \Weline\Framework\Setup\Data\Context($module_name, $version);

            $this->printer->note($remove_object->setup($this->setup_tool, $setup_context));
        } else {
            $this->printer->warning('模块卸载脚本不存在，已跳过卸载脚本！', '卸载');
        }
        $this->printer->note('2、备份应用程序...');
        if (is_dir(APP_PATH . $module_list[$module_name]['path'] . DIRECTORY_SEPARATOR)) {
            $back_path = APP_PATH . $module_list[$module_name]['path'] . DIRECTORY_SEPARATOR;
        } elseif (is_dir($back_path = BP . 'vendor/' . $module_list[$module_name]['path'] . DIRECTORY_SEPARATOR)) {
            $back_path = BP . 'vendor/' . $module_list[$module_name]['path'] . DIRECTORY_SEPARATOR;
        } else {
            $this->printer->error("模块{$module_name}:不存在！", 'ERROR');
        }
        exec("tar -zcPf {$app_path}{$module_name}.tar.gz {$back_path}");
        $this->printer->note($app_path . $module_name . '.tar.gz');
        $this->printer->note('3、卸载应用代码...');
        exec("rm $back_path -r");
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
    public function register(string $name, string $version, string $description)
    {
        // 模块路径
        $module_path = APP_PATH . $this->helper->moduleNameToPath($this->modules, $name) . DIRECTORY_SEPARATOR;
        // 检测文件完整
        $router = '';
        foreach (DataInterface::files as $filename) {
            $filepath = $module_path . $filename;
            if (is_file($filepath)) {
                if ($filename === DataInterface::file_etc_Env) {
                    $env = (array)require $filepath;
                    if (! isset($env['router'])) {
                        // 如果文件不存在则读取模块名字作为router
                        $env['router'] = strtolower($name);
                        if (DEV) {
                            $this->printer->note($name . '：模块没有设定路由别名，因此沿用模块名称作为路由入口！', '开发');
                            $this->printer->warning('{http://demo.com/' . $name . '}', '示例');
                            $this->printer->warning('设置路由别名请到：模块目录下的etc/env.php,修改return ["router"=>"' . $name . '"];', '提示');
                        }
                    }
                    $router = $env['router'];
                }
            }
        }
        if (! isset($this->setup_tool)) {
            $this->setup_tool = new \Weline\Framework\Setup\Data\Setup();
        }
        $this->setup_context = new \Weline\Framework\Setup\Data\Context($name, $version);

        $setup_dir = $module_path . \Weline\Framework\Setup\Data\DataInterface::dir;

        // 已经存在模块则更新
        if ($this->helper->isInstalled($this->modules, $name)) {
            // 是否更新模块：是则加载模块下的Setup模块下的文件进行更新
            if ($this->helper->isUpgrade($this->modules, $name, $version)) {
                $this->printer->note("扩展{$name}升级中...");
                $this->printer->setup(__('升级') . $this->modules[$name]['version'] . __('到') . $version);
                foreach (\Weline\Framework\Setup\Data\DataInterface::upgrade_FILES as $upgrade_FILE) {
                    $setup_file = $setup_dir . DIRECTORY_SEPARATOR . $upgrade_FILE . '.php';
                    if (file_exists($setup_file)) {
                        // 获取命名空间
                        $setup_file_arr = explode(APP_PATH, $setup_file);
                        $file_namespace = rtrim(str_replace(DIRECTORY_SEPARATOR, '\\', array_pop($setup_file_arr)), '.php');
                        $setup          = new $file_namespace();
                        $result         = $setup->setup($this->setup_tool, $this->setup_context);
                        $this->printer->note("{$result}");
                    }
                }
                $this->modules[$name]['version']     = $version ? $version : '1.0.0';
                $this->modules[$name]['description'] = $description ? $description : '';
                // 更新模块
                $this->helper->updateModules($this->modules);
            }
            if ($this->helper->isDisabled($this->modules, $name)) {
                echo $this->printer->warning(str_pad($name, 45) . '已禁用！');

                return;
            }
            // 更新路由
            $this->helper->registerModuleRouter($this->modules, $name, $router);
            echo $this->printer->success(str_pad($name, 45) . '已更新！');
        } else {
            $this->printer->note("扩展{$name}安装中...");
            // 全新安装
            $moduleData = [
                'status'      => 1,
                'version'     => $version ? $version : '1.0.0',
                'router'      => $router,
                'description' => $description ? $description : '',
                'path'        => $this->helper->moduleNameToPath($this->modules, $name),
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
                        $setup          = ObjectManager::getInstance($file_namespace);
                        $setup->setup($this->setup_tool, $this->setup_context);
                    }
                    $this->printer->success(str_pad($name, 45) . '已安装！');
                }
            } catch (Exception $exception) {
                throw $exception;
            }

            // 更新模块
            $this->helper->updateModules($this->modules);

            // 更新路由
            $this->helper->registerModuleRouter($this->modules, $name, $router);
        }
    }
}
