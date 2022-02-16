<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\View;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Exception\Core;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\System\Security\Encrypt;
use Weline\Framework\View\Data\DataInterface;
use Weline\Framework\View\Data\HtmlInterface;

trait TraitTemplate
{
    /**
     * @DESC          # 读取页头代码
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/14 23:24
     * 参数区：
     * @return HtmlInterface|string
     */
    function getHeader(): HtmlInterface|string
    {
        return $this->fetchClassObject('header');
    }

    /**
     * @DESC          # 读取页脚代码
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/14 23:26
     * 参数区：
     * @return HtmlInterface|string
     */
    function getFooter(): HtmlInterface|string
    {
        return $this->fetchClassObject('footer');
    }

    private function fetchClassObject(string $position)
    {
        $is_backend = $this->_request->isBackend();
        $cache_key = ($is_backend ? 'backend' : 'frontend') . "_{$position}_object";
        if (PROD && $object = $this->viewCache->get($cache_key)) {
            return $object;
        }
        $this->eventsManager->dispatch("Framework_View::{$position}", ['is_backend' => $is_backend, 'class' => '']);
        $class = $this->eventsManager->getEventData("Framework_View::{$position}")->getData('class');
        if (empty($class) || !class_exists($class)) return '';
        $object = ObjectManager::getInstance($class);
        if (PROD) $this->viewCache->set($cache_key, $object);
        return $object;
    }

    /**--------------------------资源处理------------------------------*/

    function processFileSource(string $fileName, string $file_dir): array
    {
        $view_dir = $this->view_dir;
        $template_dir = $this->template_dir;
        $compile_dir = $this->compile_dir;
        if (is_int(strpos($fileName, '::'))) {
            $pre_module_name = substr($fileName, 0, strpos($fileName, '::'));
            # 到模块配置中获取模块的模板文件路径
            $module_lists = Env::getInstance()->getModuleList();
            if (!isset($module_lists[$pre_module_name])) throw new Exception(__('异常：你指定的模板文件所在的模块不存在！模块：%1，所使用的模板：%2', [$pre_module_name, $fileName]));
            $fileName = str_replace($pre_module_name . '::', '', $fileName);
            # 替换掉当前模块的视图目录
            $view_dir = BP . $module_lists[$pre_module_name]['base_path'] . Data\DataInterface::dir . DIRECTORY_SEPARATOR;
            $template_dir = BP . $module_lists[$pre_module_name]['base_path'] . Data\DataInterface::dir . DIRECTORY_SEPARATOR . Data\DataInterface::dir_type_TEMPLATE . DIRECTORY_SEPARATOR;

            if (PROD) {
                $compile_dir = str_replace(APP_CODE_PATH, Env::path_framework_generated_complicate . DIRECTORY_SEPARATOR, $module_lists[$pre_module_name]['base_path']) . Data\DataInterface::dir . DIRECTORY_SEPARATOR . Data\DataInterface::dir_type_TEMPLATE . DIRECTORY_SEPARATOR;
            } else {
                $compile_dir = BP . $module_lists[$pre_module_name]['base_path'] . Data\DataInterface::dir . DIRECTORY_SEPARATOR . Data\DataInterface::dir_type_TEMPLATE_COMPILE . DIRECTORY_SEPARATOR;
            }
            # 文件目录
            $file_dir = str_replace($pre_module_name . '::', '', $file_dir);
        }
        return [$fileName, $file_dir, $view_dir, $template_dir, $compile_dir];
    }

    function processModuleSourceFilePath(string $type, string $source): array
    {
        $t_f = $type . DIRECTORY_SEPARATOR . $source;
        $t_f_arr = [];
        # 如果存在向别的模块调用模板的情况
        if (is_int(strpos($source, "::"))) {
            $t_f_arr = explode("::", $source);
            if (count($t_f_arr) > 1) {
                if (strpos($t_f_arr[1], $type)) {
                    $t_f_arr[2] = $t_f_arr[1];
                    $t_f_arr[1] = "::" . DIRECTORY_SEPARATOR;
                } else {
                    $t_f_arr[2] = $t_f_arr[1];
                    $t_f_arr[1] = "::" . $type . DIRECTORY_SEPARATOR;
                }
                $t_f = implode("", $t_f_arr);
            }
        };
        return [$t_f, array_shift($t_f_arr)];
    }

    public function fetchTagSourceFile(string $type, string $source)
    {
        $source = trim($source);
        $cache_key = $type . '_' . $source;
        $data = '';
        switch ($type) {
            case DataInterface::dir_type_TEMPLATE:
                if ($t_f = $this->viewCache->get($cache_key)) {
                    $data = $this->fetch($t_f);
                    break;
                }
                list($t_f,$module_name) = $this->processModuleSourceFilePath($type, $source);
                $data = $this->fetch($t_f,$module_name);
                $this->viewCache->set($cache_key, $t_f);
                break;
            case DataInterface::dir_type_STATICS:
                if ($data = $this->viewCache->get($cache_key)) {
                    break;
                }
                list($t_f, $module_name) = $this->processModuleSourceFilePath($type, $source);
                $base_url_path = rtrim($this->statics_dir, DataInterface::dir_type_STATICS);
                # 第三方模组
                if ($module_name) {
                    $modules = Env::getInstance()->getModuleList();
                    if (isset($modules[$module_name]) && $module = $modules[$module_name]) {
                        $module_view_dir_path = BP . $module['base_path'] . DataInterface::dir . DIRECTORY_SEPARATOR;
                        $base_url_path = $this->getModuleViewDir($module_view_dir_path, DataInterface::view_STATICS_DIR);
                        $t_f = str_replace($module_name . '::', '', $t_f);
                    }
                }
                $data = rtrim($this->getUrlPath($base_url_path), DataInterface::dir_type_STATICS) . DIRECTORY_SEPARATOR . $t_f;
                $this->viewCache->set($cache_key, $data);
                break;
            default:
        }
        if ($data) {
            $data = str_replace('\\', '/', $data);
            $data = str_replace('//', '/', $data);
        }
        return $data;
    }

    /**
     * @DESC          # 读取模板标签资源
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/13 20:45
     * 参数区：
     * @param string $type
     * @param string $source
     * @return bool|string|void
     * @throws Core
     */
    public function fetchTagSource(string $type, string $source)
    {
//        # 管道解析
//        $pipes = [];
//        if (is_int(strrpos($source, '|'))) {
//            $pipe_str = explode(',', substr($source, strrpos($source, '|') + 1, strlen($source)));
//            foreach ($pipe_str as $pipe) {
//                $pip = explode(':', $pipe);
//                if (2 === count($pip)) {
//                    $pipes[$pip[0]] = $pip[1];
//                }
//            }
//            $source = substr($source, 0, strrpos($source, '|'));
//        }

        $source = trim($source);
        $cache_key = $type . '_' . $source;
        if (PROD && $data = $this->viewCache->get($cache_key)) return $data;
        switch ($type) {
            case DataInterface::dir_type_STATICS:
                list($t_f, $module_name) = $this->processModuleSourceFilePath($type, $source);
                $base_url_path = rtrim($this->statics_dir, DataInterface::dir_type_STATICS);
                # 第三方模组
                if ($module_name) {
                    $modules = Env::getInstance()->getModuleList();
                    if (isset($modules[$module_name]) && $module = $modules[$module_name]) {
                        $module_view_dir_path = BP . $module['base_path'] . DataInterface::dir . DIRECTORY_SEPARATOR;
                        $base_url_path = $this->getModuleViewDir($module_view_dir_path, DataInterface::view_STATICS_DIR);
                        $t_f = str_replace($module_name . '::', '', $t_f);
                    } else {
                        throw new Exception(__('资源不存在：%1，模组：%2', [$source, $module_name]));
                    }
                }
                $data = rtrim($this->getUrlPath($base_url_path), DataInterface::dir_type_STATICS) . DIRECTORY_SEPARATOR . $t_f;
                break;
            case DataInterface::dir_type_BASE:
            case DataInterface::dir_type_TEMPLATE:
            default:
                list($t_f, $module_name) = $this->processModuleSourceFilePath($type, $source);
                $data = $this->getFetchFile($t_f, $module_name);
                break;
        }

        # 是否静态文件添加
        if ($type === 'statics' && Env::getInstance()->getConfig('static_file_rand_version')) {
            $version = random_int(10000, 100000);
            $data = str_replace('\\', '/', $data);
            $data = str_replace('//', '/', $data);
            $data .= '?v=' . $version;
        }
//        # 1、检测管道是否需要缓存或者启用
//        if ($pipes) {
//            foreach ($pipes as $pipe_attribute => $pipe_value) {
//                switch ($pipe_attribute):
//                    case 'cache':
//                        break;
//                    case 'ifconfig':
//                        /**@var SystemConfig $systemConfig */
//                        $systemConfig = ObjectManager::getInstance(SystemConfig::class);
//                        if ($systemConfig->getConfig($pipe_value, $module_name, $this->_request->isBackend() ? 'backend' : 'frontend')) {
//
//                        }
//                    default:
//
//                    }
//        }
        $this->viewCache->set($cache_key, $data);
        return $data;
    }

    /**
     * @DESC         |按照类型获取view目录
     *
     * 参数区：
     *
     * @param string $type
     * @return string
     */
    private function getViewDir(string $type = ''): string
    {
        return $this->getModuleViewDir($this->view_dir, $type);
    }

    private function getModuleViewDir(string $module_view_dir_path, string $type)
    {
        switch ($type) {
            case DataInterface::dir_type_TEMPLATE:
                $path = $module_view_dir_path . DataInterface::view_TEMPLATE_DIR;

                break;
            case DataInterface::dir_type_TEMPLATE_COMPILE:
                if (PROD) {
                    $path = str_replace(APP_CODE_PATH, Env::path_framework_generated_complicate . DIRECTORY_SEPARATOR, $module_view_dir_path) . DIRECTORY_SEPARATOR . DataInterface::view_TEMPLATE_DIR . DIRECTORY_SEPARATOR;
                } else {
                    $path = $module_view_dir_path . DataInterface::view_TEMPLATE_COMPILE_DIR;
                }
                break;
            case DataInterface::dir_type_STATICS:
                $cache_key = 'getViewDir' . $module_view_dir_path . $type;
                if ($cache_static_dir = $this->viewCache->get($cache_key)) {
                    return $cache_static_dir;
                }
                $path = $module_view_dir_path . DataInterface::view_STATICS_DIR . DIRECTORY_SEPARATOR;

                # 非开发环境
                if (PROD) {
                    $path = str_replace(APP_CODE_PATH, PUB . 'static' . DIRECTORY_SEPARATOR . $this->theme['path'] . DIRECTORY_SEPARATOR, $path);
                    $path = str_replace(VENDOR_PATH, PUB . 'static' . DIRECTORY_SEPARATOR . $this->theme['path'] . DIRECTORY_SEPARATOR, $path);
                }
                $this->viewCache->set($cache_key, $path);
                break;
            default:
                $path = $module_view_dir_path;

                break;
        }
        $path = $path . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path, 0770, true);
        }

        return $path;
    }

    /**
     * @DESC         |转化静态文件的URL路径
     *
     * 参数区：
     *
     * @param string $real_path
     * @return string
     */
    private function getUrlPath(string $real_path): string
    {
        $url_path = '';
        if (DEV) {
            if (is_int(strpos($real_path, APP_CODE_PATH))) {
                $url_path = rtrim(str_replace('\\', '/', DIRECTORY_SEPARATOR . str_replace(APP_CODE_PATH, '', $real_path)), '/');
            } else if (is_int(strpos($real_path, VENDOR_PATH))) {
                $url_path = rtrim(str_replace('\\', '/', DIRECTORY_SEPARATOR . str_replace(VENDOR_PATH, '', $real_path)), '/');
            }
        } else {
            # 检测模块位置
            $url_path = rtrim(str_replace('\\', '/', DIRECTORY_SEPARATOR . str_replace(PUB, '', $real_path)), '/');
        }
        return $url_path;
    }

    /**
     * @DESC         | 取得对应的文件
     *
     * 参数区：
     * @param string $filename
     * @return array|mixed|string|null
     * @throws Core
     */
    protected function fetchFile(string $filename): mixed
    {
        if ($cache_filename = $this->viewCache->get($filename)) {
            return $cache_filename;
        }
        /*---------观察者模式 检测文件是否被继承-----------*/
        $fileData = new DataObject(['filename' => $filename, 'type' => 'compile']);
        $this->eventsManager->dispatch(
            'Framework_View::fetch_file',
            ['object' => $this, 'data' => $fileData]
        );
        $event_filename = $fileData->getData('filename');
        $this->viewCache->set($filename, $event_filename);
        return $event_filename;
    }

}