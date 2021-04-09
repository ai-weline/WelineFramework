<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Router;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;

class Core
{
    const dir_static = 'static';

    private ?Env $_etc;

    private Request $request;

    private string $request_area;

    private string $area_router;

    private bool $is_admin;

    public function __construct(
        Request $request
    ) {
        $this->request      = $request;
        $this->request_area = $this->request->getRequestArea();
        $this->area_router  = $this->request->getAreaRouter();
        $this->_etc         = Env::getInstance();
        $area_tower         = strtolower($this->request_area);
        $this->is_admin     = strstr($area_tower, \Weline\Framework\Router\DataInterface::area_BACKEND) ? true : false;
    }

    /**
     * @DESC         |路由处理
     *
     * 参数区：
     *
     * @throws Exception
     */
    public function start()
    {
        // 读取url
        $url = $this->request->getUrl();

        // 前后台路由处理
        if ($this->is_admin) {
            if ($this->area_router === $this->_etc->getConfig('admin', '')) {
                $url = str_replace($this->area_router, 'admin', $url);
                $url = trim($url, '/');
                if (! strstr($url, '/')) {
                    $url .= '/index/index';
                }
            } elseif ($this->area_router === $this->_etc->getConfig('api_admin', '')) {
                $url = str_replace($this->area_router, 'admin', $url);
                $url = trim($url, '/');
                if (! strstr($url, '/')) {
                    $url .= '/index/index';
                }
            }
        }
        // 找不到则访问默认控制器
        if ('/' === $url) {
            $url = '/index/index';
        }
        $url = trim($url, '/');
        // API
        $this->Api($url);
        // PC
        $this->Pc($url);
        // 非开发模式（匹配不到任何路由将报错）
        if (! DEV) {
            return $this->request->getResponse()->noRouter();
        }
        // 开发模式(静态资源可访问app本地静态资源)
        if (DEV) {
            $static = $this->StaticFile($url);
            if ($static) {
                return $static;
            }

            throw new Exception('未知的路由！');
        }
        // 404
        return $this->request->getResponse()->noRouter();
    }

    /**
     * @DESC         |api路由
     *
     * 参数区：
     *
     * @param string $url
     * @throws Exception
     */
    public function Api(string $url)
    {
        $url          = strtolower($url);
        $is_api_admin = $this->request_area === \Weline\Framework\Controller\Data\DataInterface::type_api_BACKEND;
        if ($is_api_admin) {
            $router_filepath = Env::path_BACKEND_REST_API_ROUTER_FILE;
        } else {
            // 检测api路由
            $router_filepath = Env::path_FRONTEND_REST_API_ROUTER_FILE;
        }

        if (file_exists($router_filepath)) {
            $routers = include $router_filepath;
            $method  = '::' . strtoupper($this->request->getMethod());
            if (isset($routers[$url . $method]) || isset($routers[$url . '/index' . $method])) {
                $router   = $routers[$url . $method] ?? $routers[$url . '/index' . $method];
                $class    = json_decode(json_encode($router['class']));
                $dispatch = ObjectManager::getInstance($class->name);

                $method   = $class->method ? $class->method : 'index';
                if ((int)method_exists($dispatch, $method)) {
                    echo call_user_func([$dispatch, $method]);
                    exit(0);
                }

                throw new Exception("{$class->name}: 控制器方法 {$method} 不存在!");
            }
        }
        // 如果是API后端请求，找不到路由就直接404
        if ($is_api_admin) {
            $this->request->getResponse()->noRouter();
        }
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $url
     * @throws Exception
     */
    public function Pc(string $url)
    {
        $url         = strtolower($url);
        $is_pc_admin = $this->request_area === \Weline\Framework\Controller\Data\DataInterface::type_pc_BACKEND;
        if ($is_pc_admin) {
            $router_filepath = Env::path_BACKEND_PC_ROUTER_FILE;
        } else {
            // 检测api路由
            $router_filepath = Env::path_FRONTEND_PC_ROUTER_FILE;
        }
        if (is_file($router_filepath)) {
            $routers = include $router_filepath;
            if (isset($routers[$url]) || isset($routers[$url . '/index']) || isset($routers[$url . '/index/index'])) {
                $router = $routers[$url] ?? $routers[$url . '/index'] ?? $routers[$url . '/index/index'];

                $class = json_decode(json_encode($router['class']));

                // 检测注册方法
                $dispatch = ObjectManager::getInstance($class->name);
                $method   = $class->method ? $class->method : 'index';
                if (method_exists($dispatch, $method)) {
                    echo call_user_func([$dispatch, $method], $this->request->getParams());
                    exit(0);
                }

                throw new Exception("{$class->name}: 控制器方法 {$method} 不存在!");
            }
        }
        // 如果是PC后端请求，找不到路由就直接404
        if ($is_pc_admin) {
            $this->request->getResponse()->noRouter();
        }
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $url
     * @return bool|mixed
     */
    public function StaticFile(string &$url)
    {
        $filename = APP_PATH . trim($url, DIRECTORY_SEPARATOR);

        // 阻止读取其他文件
        if (is_bool(strpos($filename, \Weline\Framework\View\Data\DataInterface::dir))) {
            $this->request->getResponse()->noRouter();
        }
        if (is_file($filename)) {
            $filename_arr = explode('.', $filename);
            $file_ext     = end($filename_arr);
            if ($file_ext === 'css' || $file_ext === 'less' || $file_ext === 'sass') {
                $mime_type = 'text/css';
            } else {
                $fi        = new \finfo(FILEINFO_MIME_TYPE);
                $mime_type = $fi->file($filename);
            }
            header('Content-Type:' . $mime_type);

            return readfile($filename);
        }

        return false;
    }
}
