<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Router;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;

class Core
{
    const dir_static = 'static';

    const default_index_url = '/index/index';

    const url_path_split = '/';

    private ?Env $_etc;

    private Request $request;

    private string $request_area;

    private string $area_router;

    private bool $is_admin;

    /**
     * @DESC         |任何时候都会初始化
     *
     * 参数区：
     *
     * @throws Exception
     * @throws \ReflectionException
     */
    public function __init()
    {
        $this->request = ObjectManager::getInstance(Request::class);
        $this->request_area = $this->request->getRequestArea();
        $this->area_router = $this->request->getAreaRouter();
        $this->_etc = Env::getInstance();
        $area_tower = strtolower($this->request_area);
        $this->is_admin = (bool)strstr($area_tower, \Weline\Framework\Router\DataInterface::area_BACKEND);
    }

    /**
     * @DESC         |路由处理
     *
     * 参数区：
     *
     * @throws Exception
     * @throws \ReflectionException
     */
    public function start()
    {
        // 读取url
        $url = $this->request->getUrlPath();
        // 前后台路由处理
        if ($this->is_admin) {
            if ($this->area_router === $this->_etc->getConfig('admin', '')) {
                $url = str_replace($this->area_router, 'admin', $url);
                $url = trim($url, self::url_path_split);
                if (!strstr($url, self::url_path_split)) {
                    $url .= self::default_index_url;
                }
            } elseif ($this->area_router === $this->_etc->getConfig('api_admin', '')) {
                $url = str_replace($this->area_router, 'admin', $url);
                $url = trim($url, self::url_path_split);
                if (!strstr($url, self::url_path_split)) {
                    $url .= self::default_index_url;
                }
            }
        }

        // 找不到则访问默认控制器
        if (self::url_path_split === $url) {
            $url = self::default_index_url;
        }
        $url = trim($url, self::url_path_split);
        // API
        if ($api_result = $this->Api($url)) {
            return $api_result;
        }
        // PC
        if ($pc_result = $this->Pc($url)) {
            return $pc_result;
        }
        // 非开发模式（匹配不到任何路由将报错）
        if (!DEV) {
            $this->request->getResponse()->noRouter();
        } else {
            // 开发模式(静态资源可访问app本地静态资源)
            $static = $this->StaticFile($url);
            if ($static) {
                return $static;
            }
            throw new Exception('未知的路由！');
        }
    }

    /**
     * @DESC         |api路由
     *
     * 参数区：
     *
     * @param string $url
     * @return false|void
     * @throws Exception
     * @throws \ReflectionException
     */
    public function Api(string $url)
    {
        $url = strtolower($url);
        $is_api_admin = $this->request_area === \Weline\Framework\Controller\Data\DataInterface::type_api_BACKEND;

        if ($is_api_admin) {
            $router_filepath = Env::path_BACKEND_REST_API_ROUTER_FILE;
        } else {
            // 检测api路由
            $router_filepath = Env::path_FRONTEND_REST_API_ROUTER_FILE;
        }

        if (file_exists($router_filepath)) {
            $routers = include $router_filepath;
            $method = '::' . strtoupper($this->request->getMethod());
            if (isset($routers[$url . $method]) || isset($routers[$url . '/index' . $method])) {
                $router = $routers[$url . $method] ?? $routers[$url . '/index' . $method];
                $class = json_decode(json_encode($router['class']));
                $dispatch = ObjectManager::getInstance($class->name);
                $this->request->setRouter($router);
                $method = $class->method ? $class->method : 'index';
                if ((int)method_exists($dispatch, $method)) {
                    exit(call_user_func([$dispatch, $method]));
                }

                throw new Exception("{$class->name}: 控制器方法 {$method} 不存在!");
            }
        }
        // 如果是API后端请求，找不到路由就直接404
        if ($is_api_admin) {
            $this->request->getResponse()->noRouter();
        }

        return false;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $url
     * @return false|void
     * @throws Exception
     * @throws \ReflectionException
     */
    public function Pc(string $url)
    {
        $url = strtolower($url);
        $is_pc_admin = $this->request_area === \Weline\Framework\Controller\Data\DataInterface::type_pc_BACKEND;
        // 检测api路由区域
        if ($is_pc_admin) {
            $router_filepath = Env::path_BACKEND_PC_ROUTER_FILE;
        } else {
            $router_filepath = Env::path_FRONTEND_PC_ROUTER_FILE;
        }
        if (is_file($router_filepath)) {
            $routers = include $router_filepath;
            if (isset($routers[$url]) || isset($routers[$url . '/index']) || isset($routers[$url . self::default_index_url])) {
                $router = $routers[$url] ?? $routers[$url . '/index'] ?? $routers[$url . self::default_index_url];

                $class = json_decode(json_encode($router['class']));
                $this->request->setRouter($router);
                // 检测注册方法
                $dispatch = ObjectManager::getInstance($class->name);
                $method = $class->method ?: 'index';
                if (method_exists($dispatch, $method)) {
                    return call_user_func([$dispatch, $method], $this->request->getParams());
                }

                throw new Exception(__("%1}: 控制器方法 %2 不存在!", [$class->name, $method]));
            }
        }
        // 如果是PC后端请求，找不到路由就直接404
        if ($is_pc_admin) {
            $this->request->getResponse()->noRouter();
        }

        return false;
    }

    /**
     * @DESC         |返回开发静态文件
     *
     * 参数区：
     *
     * @param string $url
     * @return mixed
     * @throws Exception
     * @throws \ReflectionException
     */
    public function StaticFile(string &$url): mixed
    {
        $filename = APP_PATH . trim($url, DIRECTORY_SEPARATOR);

        // 阻止读取其他文件
        if (is_bool(strpos($filename, \Weline\Framework\View\Data\DataInterface::dir))) {
            $this->request->getResponse()->noRouter();
        }
        if (is_file($filename)) {
            $filename_arr = explode('.', $filename);
            $file_ext = end($filename_arr);
            if ($file_ext === 'css' || $file_ext === 'less' || $file_ext === 'sass') {
                $mime_type = 'text/css';
            } else {
                $fi = new \finfo(FILEINFO_MIME_TYPE);
                $mime_type = $fi->file($filename);
            }
            header('Content-Type:' . $mime_type);

            return readfile($filename);
        }

        return false;
    }
}
