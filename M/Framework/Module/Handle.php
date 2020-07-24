<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/20
 * 时间：17:02
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Module;


use M\Framework\App\Etc;
use M\Framework\App\Exception;
use M\Framework\Console\ConsoleException;
use M\Framework\Console\Module\Upgrade;
use M\Framework\FileSystem\App\Scanner;
use M\Framework\FileSystem\Io\File;
use M\Framework\Helper\AbstractHelper;
use M\Framework\Helper\HandleInterface;
use M\Framework\Module\Helper\Data;
use M\Framework\Module\Must\DataInterface;
use M\Framework\Output\Cli\Printing;
use M\Framework\Setup\Helper\Data as SetupHelper;

class Handle implements HandleInterface
{
    private \M\Framework\Setup\Data\Setup $setup_tool;
    private \M\Framework\Setup\Data\Context $setup_context;

    const api_DIR = 'Api';// api特殊目录，注册api路由

    const pc_DIR = 'Controller';// pc特殊目录，注册pc路由
    private Printing $printer;
    private array $modules;

    private string $modulesFilePath;
    private SetupHelper $setup_helper;
    private File $file;
    private Data $helper;

    function __construct()
    {
        $this->modules = Etc::getInstance()->getModuleList();
        $this->printer = new Printing();
        $this->helper = new Data();
    }

    function getHelper(): AbstractHelper
    {
        return $this->helper;
    }

    public function upgrade()
    {
        $this->helper->updateAllModuleRouters();
    }

    public function disable($name)
    {
        if (array_key_exists($name, $this->modules)) {
            // 扫描模块中是否api目录，注册api路由映射
            $appScanner = new Scanner();
            $moduleDir = $appScanner->scanDir();
        }
    }

    public function enable($name)
    {
        if (array_key_exists($name, $this->modules)) {
            // 扫描模块中是否api目录，注册api路由映射
            $appScanner = new Scanner();
            $moduleDir = $appScanner->scanDir();
        }
    }

    /**
     * @DESC         |移除应用
     *
     * 参数区：
     *
     * @param string $module_name
     * @throws \M\Framework\App\Exception
     */
    public function remove(string $module_name)
    {
        $module_list = Etc::getInstance()->getModuleList();
        if (!isset($this->setup_tool)) $this->setup_tool = new \M\Framework\Setup\Data\Setup();
        if (!isset($this->setup_helper)) $this->setup_helper = new SetupHelper();
        $app_path = APP_PATH;
        $this->printer->note(__('1、正在执行卸载脚本...'));
        $remove_script = $this->setup_helper->getSetupClass($module_name, \M\Framework\Setup\Data\DataInterface::type_REMOVE);
        $remove_object = new $remove_script();

        $version = isset($module_list[$module_name]['version']) ? $module_list[$module_name]['version'] : '1.0.0';
        $setup_context = new \M\Framework\Setup\Data\Context($module_name, $version);

        $this->printer->note($remove_object->setup($this->setup_tool, $setup_context));

        $this->printer->note('2、备份应用程序...');
        $back_path = APP_PATH . $module_list[$module_name]['path'] . DIRECTORY_SEPARATOR;
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
     * @throws \M\Framework\App\Exception
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
            if (!is_file($filepath)) throw new Exception($filepath . ' 文件不存在！');
            if ($filename == DataInterface::file_etc_Env) {
                $env = (array)require $filepath;
                if (!isset($env['router'])) throw new Exception($filepath . ' 文件中未配置router！');
                $router = $env['router'];
            };
        }

        if (!isset($this->setup_tool)) $this->setup_tool = new \M\Framework\Setup\Data\Setup();
        $this->setup_context = new \M\Framework\Setup\Data\Context($name, $version);

        $setup_dir = $module_path . \M\Framework\Setup\Data\DataInterface::dir;

        // 已经存在模块则更新
        if ($this->helper->isInstalled($this->modules, $name)) {
            // 是否更新模块：是则加载模块下的Setup模块下的文件进行更新
            if ($this->helper->isUpgrade($this->modules, $name, $version)) {
                $this->printer->note("扩展{$name}升级中...");
                $this->printer->setup(__('升级') . $this->modules[$name]['version'] . __('到') . $version);
                foreach (\M\Framework\Setup\Data\DataInterface::upgrade_FILES as $upgrade_FILE) {
                    $setup_file = $setup_dir . DIRECTORY_SEPARATOR . $upgrade_FILE . '.php';
                    if (file_exists($setup_file)) {
                        // 获取命名空间
                        $setup_file_arr = explode(APP_PATH, $setup_file);
                        $file_namespace = rtrim(str_replace(DIRECTORY_SEPARATOR, '\\', array_pop($setup_file_arr)), '.php');
                        $setup = new $file_namespace();
                        $result = $setup->setup($this->setup_tool, $this->setup_context);
                        $this->printer->note("{$result}");
                    }
                }
                $this->modules[$name]['version'] = $version ? $version : '1.0.0';
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
            $moduleData = array(
                'status' => 1,
                'version' => $version ? $version : '1.0.0',
                'router' => $router ?? '',
                'description' => $description ? $description : '',
                'path' => $this->helper->moduleNameToPath($this->modules, $name)
            );
            $this->modules[$name] = $moduleData;
            // 更新模块
            $this->helper->updateModules($this->modules);
            // 更新路由
            $this->helper->registerModuleRouter($this->modules, $name, $router);
            // 安装模块：加载模块下的Setup模块下的安装文件进行安装
            foreach (\M\Framework\Setup\Data\DataInterface::install_FILES as $install_FILE) {
                $setup_file = $setup_dir . DIRECTORY_SEPARATOR . $install_FILE . '.php';
                if (file_exists($setup_file)) {
                    // 获取命名空间
                    $setup_file_arr = explode(APP_PATH, $setup_file);
                    $file_namespace = rtrim(str_replace(DIRECTORY_SEPARATOR, '\\', array_pop($setup_file_arr)), '.php');
                    $setup = new $file_namespace();
                    $setup->setup($this->setup_tool, $this->setup_context);
                }
                $this->printer->success(str_pad($name, 45) . '已安装！');
            }
        }
    }


}