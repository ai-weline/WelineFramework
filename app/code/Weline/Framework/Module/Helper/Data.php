<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Module\Helper;

use Weline\Framework\App\Env;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\System\File\App\Scanner;
use Weline\Framework\System\File\Io\File;
use Weline\Framework\Helper\AbstractHelper;
use Weline\Framework\Http\RequestInterface;
use Weline\Framework\Module\Handle;
use Weline\Framework\Register\Register;
use Weline\Framework\Register\Router\Data\DataInterface;

class Data extends AbstractHelper
{
    private array $parent_class_arr = [];
    private File $file;

    function __init()
    {
        $this->file = ObjectManager::getInstance(File::class);
    }

    function getClassNamespace(\Weline\Framework\System\File\Data\File $controllerFile)
    {
        $namespace_arr = explode('\\', $controllerFile->getNamespace());
        foreach ($namespace_arr as &$item) {
            if (is_int(strpos($item, '-'))) {
                $item = explode('-', $item);
                foreach ($item as &$i) {
                    $i = ucfirst($i);
                }
                $item = implode('', $item);
            }
            $item = ucfirst($item);
        }
        return implode('\\', $namespace_arr);
    }

    /**
     * @DESC         |注册模块路由
     *
     * 参数区：
     *
     * @param array  $modules
     * @param string $name
     * @param string $router
     *
     * @throws \Weline\Framework\App\Exception
     * @throws \Weline\Framework\Console\ConsoleException
     * @throws \ReflectionException
     */
    public function registerModuleRouter(array &$modules, string $path, string $name, string $router)
    {
        if (!$this->isDisabled($modules, $name)) {
            // 禁用则不进行注册
            /**@var Scanner $appScanner */
            $appScanner = ObjectManager::getInstance(Scanner::class);
            // 扫描模块
            $appScanner->__init();

            $moduleDir = $appScanner->scanDirTree(BP . $path);
            /**@var Register $routerRegister */
            $routerRegister = ObjectManager::getInstance(Register::class);
            /** @var $Files \Weline\Framework\System\File\Data\File[] */

            foreach ($moduleDir as $dir => $Files) {
                // Api路由
                if (!is_bool(strpos($dir, Handle::api_DIR))) {
                    foreach ($Files as $apiFile) {
                        $apiDirArray  = explode(Handle::api_DIR, $dir . DIRECTORY_SEPARATOR . $apiFile->getFilename());
                        $baseRouter   = str_replace('\\', '/', strtolower(array_pop($apiDirArray)));
                        $baseRouter   = $router . ($baseRouter ?: '');
                        $baseRouter   = trim($baseRouter, '/');
                        $apiClassName = $this->getClassNamespace($apiFile) . '\\' . $apiFile->getFilename();
                        $apiClassName = str_replace("\\\\", "\\", $apiClassName);
                        // 删除父类方法：注册控制器方法
                        $this->parent_class_arr = [];// 清空父类信息
                        $ctl_data               = $this->parserController($apiClassName);
                        if (empty($ctl_data)) {
                            continue;
                        }
                        $ctl_methods = $ctl_data['methods'];
                        $ctl_area    = $ctl_data['area'];
                        foreach ($ctl_methods as $method) {
                            // 分析请求方法
                            $request_method_split_array = preg_split('/(?=[A-Z])/', $method);
                            $request_method             = array_shift($request_method_split_array);
                            $class_method               = strtolower(str_replace($request_method, '', $method));
                            $request_method             = strtoupper($request_method);
                            $request_method             = $request_method ?? RequestInterface::GET;
                            $request_method             = strtoupper($request_method);
                            if (!in_array($request_method, RequestInterface::METHODS, true)) {
                                $request_method = RequestInterface::GET;
                            }
                            // 路由注册+
                            Register::register(Register::ROUTER, [
                                'type'           => DataInterface::type_API,
                                'area'           => $ctl_area,
                                'module'         => $name,
                                'base_router'    => $router,
                                'router'         => $baseRouter . ($class_method ? '/' . $class_method : '') . '::' . $request_method,
                                'class'          => $apiClassName,
                                'module_path'    => $path,
                                'method'         => $method,
                                'request_method' => $request_method,
                            ]);
                        }
                    }
                } // PC路由
                elseif (!is_bool(strpos($dir, Handle::pc_DIR))) {
                    foreach ($Files as $controllerFile) {

                        $controllerDirArray = explode(Handle::pc_DIR, $dir . DIRECTORY_SEPARATOR . $controllerFile->getFilename());
                        $baseRouter         = str_replace('\\', '/', strtolower(array_pop($controllerDirArray)));
                        $baseRouter         = $router . ($baseRouter ?? '');
                        $baseRouter         = trim($baseRouter, '/');

                        $controllerClassName = $this->getClassNamespace($controllerFile) . '\\' . $controllerFile->getFilename();
                        $controllerClassName = str_replace("\\\\", "\\", $controllerClassName);
                        // 删除父类方法：注册控制器方法
                        $this->parent_class_arr = [];// 清空父类信息
                        $ctl_data               = $this->parserController($controllerClassName);
                        if (empty($ctl_data)) {
                            continue;
                        }
                        $ctl_methods = $ctl_data['methods'];
                        $ctl_area    = $ctl_data['area'];

                        foreach ($ctl_methods as $method) {
                            // 分析请求方法
                            $request_method_split_array = preg_split('/(?=[A-Z])/', $method);
                            $request_method             = array_shift($request_method_split_array);
                            $request_method_name        = array_shift($request_method_split_array);
                            $request_method             = $request_method ?? RequestInterface::GET;
                            $request_method             = strtoupper($request_method);
                            if (!in_array($request_method, RequestInterface::METHODS, true)) {
                                $request_method = '';
                            }

                            Register::register(Register::ROUTER, [
                                'type'           => DataInterface::type_PC,
                                'area'           => $ctl_area,
                                'module'         => $name,
                                'base_router'    => $router,
                                'router'         => $baseRouter . '/' . strtolower($request_method_name ?? $method),
                                'class'          => $controllerClassName,
                                'method'         => $method,
                                'module_path'    => $path,
                                'request_method' => $request_method,
                            ]);
                        }
                    }
                }
            }
        }
    }

    /**
     * @DESC         |模块名到路径转化
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array  $modules
     * @param string $name
     *
     * @return string
     */
    public function moduleNameToPath(array &$modules, string $name): string
    {
        if ($this->isInstalled($modules, $name)) {
            return trim(str_replace('\\', DIRECTORY_SEPARATOR, $modules[$name]['path']), DIRECTORY_SEPARATOR);
        }

        return str_replace('_', DIRECTORY_SEPARATOR, $name);
    }

    /**
     * @DESC         |模块名到路径转化
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $name
     *
     * @return string
     */
    public function getModulePath(string $name): string
    {
        return APP_CODE_PATH . str_replace('_', DIRECTORY_SEPARATOR, $name);
    }

    /**
     * @DESC         |利用反射去除父类方法
     *
     * 参数区：
     *
     * @param string $class
     *
     * @return array
     * @throws \ReflectionException
     */
    private function parserController(string $class)
    {
        // 默认前端控制器
//        $ctl_area = \Weline\Framework\Controller\Data\DataInterface::type_pc_FRONTEND;
        if (class_exists($class)) {
            $reflect            = new \ReflectionClass($class);
            $controller_methods = [];
            foreach ($reflect->getMethods() as $method) {
                if (is_int(strpos($method->getName(), '__'))) {
                    continue;
                }
                $controller_methods[] = $method->getName();
            }
            // 存在父类则过滤父类方法
            if ($parent_class = $reflect->getParentClass()) {
                $controller_class = [];
                foreach (explode('\\', $parent_class->getName()) as $item) {
                    if (is_int(strpos($item, 'Controller'))) {
                        $controller_class[] = $item;
                    }
                }
                $this->parent_class_arr = array_merge($this->parent_class_arr, $controller_class);
                $parent_methods         = [];
                foreach ($parent_class->getMethods() as $method) {
                    if (is_int(strpos($method->getName(), '__'))) {
                        continue;
                    }
                    $parent_methods[] = $method->getName();
                }
                $controller_methods = array_diff($controller_methods, $parent_methods);
                // 实例化类
                if (!$parent_class->isAbstract()) {
                    $this->parent_class_arr = array_merge($this->parent_class_arr, $this->parserController($parent_class->getName())['area']);
                }
            }

            return ['area' => array_unique($this->parent_class_arr), 'methods' => $controller_methods];
        } else {
            return [];
        }
    }

    /**
     * @DESC         |模块是否已经安装
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array  $modules
     * @param string $name
     *
     * @return bool
     */
    public function isInstalled(array &$modules, string $name): bool
    {
        return array_key_exists($name, $modules);
    }

    /**
     * @DESC         |模块是否已经安装
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array  $modules
     * @param string $name
     *
     * @return bool
     */
    public function isDisabled(array &$modules, string $name): bool
    {
        if ($this->isInstalled($modules, $name) && isset($modules[$name]['status']) && $modules[$name]['status']) {
            return false;
        }

        return true;
    }

    /**
     * @DESC         |是否模块更新
     *
     * 参数区：
     *
     * @param array  $modules
     * @param string $name
     * @param string $version
     *
     * @return bool
     */
    public function isUpgrade(array &$modules, string $name, string $version): bool
    {
        if (version_compare($version, $modules[$name]['version'], '>')) {
            $modules[$name]['version'] = $version;

            return true;
        }

        return false;
    }

    /**
     * @DESC         |更新模块数据
     *
     * 参数区：
     *
     * @param array $modules
     */
    public function updateModules(array &$modules)
    {
        $this->file->open(Env::path_MODULES_FILE, $this->file::mode_w_add);
        $text = '<?php return ' . var_export($modules, true) . ';';
        $this->file->write($text);
        $this->file->close();
    }

    /**
     * @DESC         |更新路由
     *
     * 参数区：
     *
     * @param array $routers
     *
     * @throws \Weline\Framework\App\Exception
     */
    public function updateRouters(array &$routers)
    {
        $this->file->open(Env::path_MODULES_FILE, $this->file::mode_w_add);
        $text = '<?php return ' . var_export($routers, true) . ';';
        $this->file->write($text);
        $this->file->close();
    }
}
