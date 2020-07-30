<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/4
 * 时间：15:29
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Module\Helper;


use M\Framework\App\Env;
use M\Framework\FileSystem\App\Scanner;
use M\Framework\FileSystem\App\Scanner as AppScanner;
use M\Framework\FileSystem\Io\File;
use M\Framework\Helper\AbstractHelper;
use M\Framework\Http\RequestInterface;
use M\Framework\Module\Handle;
use M\Framework\Register\Register;
use M\Framework\Register\Router\Data\DataInterface;

class Data extends AbstractHelper
{
    /**
     * @DESC         |注册模块路由
     *
     * 参数区：
     *
     * @param array $modules
     * @param string $name
     * @param string $router
     * @return array
     * @throws \M\Framework\App\Exception
     * @throws \M\Framework\Console\ConsoleException
     * @throws \ReflectionException
     */
    function registerModuleRouter(array &$modules, string $name, string $router)
    {
        if ($this->isDisabled($modules, $name)) return [];// 禁用则不进行注册

        $appScanner = new Scanner();
        // 扫描模块
        $moduleDir = $appScanner->scanDirTree(APP_PATH . $this->moduleNameToPath($modules, $name), 12);
        $routerRegister = new Register();

        /** @var \M\Framework\FileSystem\Io\File[] $Files */
        foreach ($moduleDir as $dir => $Files) {
            // Api路由
            if (strstr($dir, Handle::api_DIR)) {
                foreach ($Files as $apiFile) {
                    $apiDirArray = explode(Handle::api_DIR, $dir . DIRECTORY_SEPARATOR . $apiFile->getFilename());
                    $baseRouter = str_replace('\\', '/', strtolower(array_pop($apiDirArray)));
                    $baseRouter = $router . ($baseRouter ?? '');
                    $baseRouter = trim($baseRouter,'/');
                    $apiClassName = $apiFile->getNamespace() . '\\' . $apiFile->getFilename();
                    $apiClass = new $apiClassName();

                    // 删除父类方法：注册控制器方法
                    $ctl_data = $this->removeParentMethods($apiClass);
                    $ctl_methods = $ctl_data['methods'];
                    $ctl_area = $ctl_data['area'];
                    foreach ($ctl_methods as $method) {
                        // 分析请求方法
                        $request_method_split_array = preg_split("/(?=[A-Z])/", $method);
                        $request_method = array_shift($request_method_split_array);
                        $class_method = strtolower(str_replace($request_method, '', $method));
                        $request_method = strtoupper($request_method);
                        $request_method = $request_method ? $request_method : RequestInterface::GET;
                        if (!in_array($request_method, RequestInterface::METHODS)) $request_method = RequestInterface::GET;
                        // 路由注册
                        $routerRegister::register(Register::ROUTER, array(
                            'type' => DataInterface::type_API,
                            'area' => $ctl_area,
                            'module' => $name,
                            'router' => $baseRouter . ($class_method ? '/' . $class_method : '') . '::' . $request_method,
                            'class' => $apiClassName,
                            'method' => $method,
                            'request_method' => $request_method
                        ));
                    }
                }
            } // PC路由
            elseif (strstr($dir, Handle::pc_DIR)) {
                foreach ($Files as $controllerFile) {
                    $controllerDirArray = explode(Handle::pc_DIR, $dir . DIRECTORY_SEPARATOR . $controllerFile->getFilename());
                    $baseRouter = str_replace('\\', '/', strtolower(array_pop($controllerDirArray)));
                    $baseRouter = $router . ($baseRouter ?? '');
                    $baseRouter = trim($baseRouter,'/');
                    $controllerClassName = $controllerFile->getNamespace() . '\\' . $controllerFile->getFilename();
                    $controllerClass = new $controllerClassName();
                    // 删除父类方法：注册控制器方法
                    $ctl_data = $this->removeParentMethods($controllerClass);
                    $ctl_methods = $ctl_data['methods'];
                    $ctl_area = $ctl_data['area'];
                    foreach ($ctl_methods as $method) {
                        // 分析请求方法
                        $request_method_split_array = preg_split("/(?=[A-Z])/", $method);
                        $request_method = array_shift($request_method_split_array);
                        $request_method = $request_method ? $request_method : RequestInterface::GET;
                        if (!in_array($request_method, RequestInterface::METHODS)) $request_method = RequestInterface::GET;
                        $routerRegister::register(Register::ROUTER, array(
                            'type' => DataInterface::type_PC,
                            'area' => $ctl_area,
                            'module' => $name,
                            'router' => $baseRouter . '/' . $method,
                            'class' => $controllerClassName,
                            'method' => $method,
                            'request_method' => $request_method
                        ));
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
     * @param array $modules
     * @param string $name
     * @return string
     */
    function moduleNameToPath(array &$modules, string $name): string
    {
        if ($this->isInstalled($modules, $name)) {
            return trim($modules[$name]['path'], DIRECTORY_SEPARATOR);
        }
        return str_replace('_', DIRECTORY_SEPARATOR, $name);
    }

    /**
     * @DESC         |利用反射去除父类方法
     *
     * 参数区：
     * @param object $class
     * @return array
     * @throws \ReflectionException
     */
    private function removeParentMethods(object $class)
    {
        // 默认前端控制器
        $ctl_area = \M\Framework\Controller\Data\DataInterface::type_pc_FRONTEND;

        $reflect = new \ReflectionClass($class);
        $controller_methods = [];
        foreach ($reflect->getMethods() as $method) {
            $controller_methods[] = $method->getName();
        }
        // 存在父类则过滤父类方法
        if ($parent_class = $reflect->getParentClass()) {
            $parent_class_arr = explode('\\', $parent_class->getName());
            $ctl_area = array_pop($parent_class_arr);
            $parent_methods = [];
            foreach ($parent_class->getMethods() as $method) {
                $parent_methods[] = $method->getName();
            }
            $controller_methods = array_diff($controller_methods, $parent_methods);
        }
        return ['area' => $ctl_area, 'methods' => $controller_methods];
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
     * @param array $modules
     * @param string $name
     * @return bool
     */
    function isInstalled(array &$modules, string $name): bool
    {
        if (array_key_exists($name, $modules)) {
            return true;
        }
        return false;
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
     * @param array $modules
     * @param string $name
     * @return bool
     */
    function isDisabled(array &$modules, string $name): bool
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
     * @param array $modules
     * @param string $name
     * @param string $version
     * @return bool
     */
    function isUpgrade(array &$modules, string $name, string $version): bool
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
    function updateModules(array &$modules)
    {
        $file = new File();
        $file->open(Env::path_MODULES_FILE, $file::mode_w_add);
        $text = '<?php return ' . var_export($modules, true) . ';';
        $file->write($text);
        $file->close();
    }

    /**
     * @DESC         |更新路由
     *
     * 参数区：
     *
     * @param array $routers
     * @throws \M\Framework\App\Exception
     */
    function updateRouters(array &$routers)
    {
        $file = new File();
        $file->open(Env::path_MODULES_FILE, $file::mode_w_add);
        $text = '<?php return ' . var_export($routers, true) . ';';
        $file->write($text);
        $file->close();
    }
}