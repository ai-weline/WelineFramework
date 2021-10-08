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
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Session\Session;
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
        $is_backend = $this->session->isBackend();
        $cache_key = ($is_backend ? 'backend' : 'frontend') . "_{$position}_object";
        if (!DEV && $object = $this->viewCache->get($cache_key)) {
            return $object;
        }
        $this->eventsManager->dispatch("Framework_View::{$position}", ['is_backend' => $is_backend, 'class' => '']);
        $class = $this->eventsManager->getEventData("Framework_View::{$position}")->getData('class');
        if (empty($class) || !class_exists($class)) return '';
        $object = ObjectManager::getInstance($class);
        if (!DEV) $this->viewCache->set($cache_key, $object);
        return $object;
    }

    /**--------------------------资源处理------------------------------*/

    function processFileSource(string $fileName, string $file_dir): array
    {
        $view_dir = $this->view_dir;
        $template_dir = $this->template_dir;
        $compile_dir = $this->compile_dir;
        if (strstr($fileName, '::')) {
            $pre_module_name = substr($fileName, 0, strpos($fileName, '::'));
            # 到模块配置中获取模块的模板文件路径
            $module_lists = Env::getInstance()->getModuleList();
            if (!isset($module_lists[$pre_module_name])) throw new Exception(__('异常：你指定的模板文件所在的模块不存在！模块：%1，所使用的模板：%2', [$pre_module_name, $fileName]));
            $fileName = str_replace($pre_module_name . '::', '', $fileName);
            # 替换掉当前模块的视图目录
            $view_dir = $module_lists[$pre_module_name]['base_path'] . Data\DataInterface::dir . DIRECTORY_SEPARATOR;
            $template_dir = $module_lists[$pre_module_name]['base_path'] . Data\DataInterface::dir . DIRECTORY_SEPARATOR . Data\DataInterface::dir_type_TEMPLATE . DIRECTORY_SEPARATOR;
            $compile_dir = $module_lists[$pre_module_name]['base_path'] . Data\DataInterface::dir . DIRECTORY_SEPARATOR . Data\DataInterface::dir_type_TEMPLATE_COMPILE . DIRECTORY_SEPARATOR;
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
        if (strstr($source, "::")) {
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
    public function fetchTemplateTagSource(string $type, string $source)
    {
        $source = trim($source);
        $cache_key = $type . '_' . $source;
        $data = '';
        switch ($type) {
            case DataInterface::dir_type_TEMPLATE:
                if (!DEV && $t_f = $this->viewCache->get($cache_key)) {
                    $data = $this->fetch($t_f);
                    break;
                }
                list($t_f) = $this->processModuleSourceFilePath($type, $source);
                $data = $this->fetch($t_f);
                if (!DEV) $this->viewCache->set($cache_key, $t_f);
                break;
            case DataInterface::dir_type_STATICS:
                if (!DEV && $data = $this->viewCache->get($cache_key)) {
                    break;
                }

                list($t_f, $module_name) = $this->processModuleSourceFilePath($type, $source);
                $base_url_path = rtrim($this->statics_dir, DataInterface::dir_type_STATICS);
                # 第三方模组
                if ($module_name) {
                    $modules = Env::getInstance()->getModuleList();
                    if (isset($modules[$module_name]) && $module = $modules[$module_name]) {
                        $module_view_dir_path = $module['base_path'] . DataInterface::dir . DIRECTORY_SEPARATOR;
                        $base_url_path = $this->getModuleViewDir($module_view_dir_path, DataInterface::view_STATICS_DIR);
                        $t_f = str_replace($module_name . '::', '', $t_f);
                    }
                }
                $data = rtrim($this->getUrlPath($base_url_path), DataInterface::dir_type_STATICS) . DIRECTORY_SEPARATOR . $t_f;
                if (!DEV) $this->viewCache->set($cache_key, $data);
                break;
            default:
        }
        if($data)$data = str_replace('\\', '', $data);
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
                $path = $module_view_dir_path . DataInterface::view_TEMPLATE_COMPILE_DIR;

                break;
            case DataInterface::dir_type_STATICS:
                $cache_key = 'getViewDir' . $module_view_dir_path . $type;
                if (!DEV && $cache_static_dir = $this->viewCache->get($cache_key)) {
                    return $cache_static_dir;
                }
                $path = $module_view_dir_path . DataInterface::view_STATICS_DIR . DIRECTORY_SEPARATOR;

                if (!DEV) {
                    $path = str_replace(APP_PATH, PUB . 'static' . DIRECTORY_SEPARATOR . $this->theme['path'] . DIRECTORY_SEPARATOR, $path);
                    $this->viewCache->set($cache_key, $path);
                }

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
        $explode_str = DEV ? APP_PATH : PUB;
        return rtrim(str_replace('\\', '/', DIRECTORY_SEPARATOR . str_replace($explode_str, '', $real_path)), '/');
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
        if (!DEV && $cache_filename = $this->viewCache->get($filename)) {
            return $cache_filename;
        }
        /*---------观察者模式 检测文件是否被继承-----------*/
        $fileData = new DataObject(['filename' => $filename, 'type' => 'compile']);
        $this->eventsManager->dispatch(
            'Framework_View::fetch_file',
            ['object' => $this, 'data' => $fileData]
        );
        $event_filename = $fileData->getData('filename');
        if (!DEV) {
            $this->viewCache->set($filename, $event_filename);
        }

        return $event_filename;
    }

}