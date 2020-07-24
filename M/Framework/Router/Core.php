<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/21
 * 时间：18:33
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Router;


use finfo;
use M\Framework\App\Etc;
use M\Framework\App\Exception;
use M\Framework\Http\Request\BaseRequest;

class Core
{
    const dir_static = 'static';
    private ?Etc $_etc;
    private static Core $instance;
    private BaseRequest $base_request;
    private string $request_area;
    private string $area_router;
    private string $is_admin;

    private function __clone()
    {

    }

    private function __construct()
    {
        $this->_etc = Etc::getInstance();
        $this->base_request = BaseRequest::getInstance();
        $this->request_area = $this->base_request->getRequestArea();
        $this->area_router = $this->base_request->getAreaRouter();
        $this->is_admin = ($this->request_area !== \M\Framework\Router\DataInterface::area_FROMTEND);
    }

    final static function getInstance()
    {
        if (!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }


    /**
     * @DESC         |路由处理
     *
     * 参数区：
     *
     * @throws Exception
     */
    function start()
    {
        // 读取url
        $url = $this->base_request->getUrl();
        // 前后台路由处理
        if ($this->is_admin) {
            if ($this->area_router === $this->_etc->getConfig('admin', '')) {
                $url = str_replace($this->area_router, '', $url);
                $url = trim($url, '/');
                if ('' == $url) {
                    $url .= '/Index/Index';
                }
            } elseif ($this->area_router === $this->_etc->getConfig('api_admin', '')) {
                $url = str_replace($this->area_router, '', $url);
                $url = trim($url, '/');
                if ('' == $url) {
                    $url .= '/Index/Index';
                }
            }
        }
        if ('/' === $url) {// 找不到则访问默认控制器
            $url = '/Index/Index';
        }
        $url = trim($url,'/');

        // API
        $this->Api($url);
        // PC
        $this->Pc($url);

        // 静态资源
        if (DEV) if ($this->StaticFile($url)) return;
        // 开发模式
        if (DEV) throw new Exception('未知的路由！');
        // 404
        $this->base_request->getResponse()->noRouter();
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
        $url = strtolower($url);
        // 检测api路由
        $router_filepath = Etc::path_FRONTEND_REST_API_ROUTER_FILE;
        $is_api_admin = $this->request_area === \M\Framework\Controller\Data\DataInterface::type_api_REST_BACKEND;
        if ($is_api_admin)
            $router_filepath = Etc::path_BACKEND_REST_API_ROUTER_FILE;
        if (file_exists($router_filepath)) {
            $routers = include $router_filepath;
            $method = '::' . strtoupper($this->base_request->getMethod());
            if (isset($routers[$url . $method]) || isset($routers[$url . '/Index' . $method])) {
                $router = isset($routers[$url . $method]) ? $routers[$url . $method] : $routers[$url . '/Index' . $method];
                $class = json_decode(json_encode($router['class']));
                $dispatch = new $class->name();
                $method = $class->method ? $class->method : 'Index';
                if ((int)method_exists($dispatch, $method)) {
//                        echo call_user_func(array($dispatch, $method), $this->getParams());
                    echo call_user_func(array($dispatch, $method));
                    exit(0);
                } else {
                    throw new Exception("{$class->name}: 控制器方法 {$method} 不存在!");
                }
            }
        }
        // 如果是API后端请求，找不到路由就直接404
        if ($is_api_admin) $this->base_request->getResponse()->noRouter();
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
        $url = strtolower($url);
        // 检测api路由
        $router_filepath = Etc::path_FRONTEND_PC_ROUTER_FILE;
        $is_pc_admin = $this->request_area === \M\Framework\Controller\Data\DataInterface::type_pc_BACKEND;
        if ($is_pc_admin)
            $router_filepath = Etc::path_BACKEND_PC_ROUTER_FILE;
        if (is_file($router_filepath)) {
            $routers = include $router_filepath;
            if (isset($routers[$url]) || isset($routers[$url . '/Index'])) {
                $router = isset($routers[$url]) ? $routers[$url] : $routers[$url . '/Index'];
                $class = json_decode(json_encode($router['class']));

                // 检测注册方法
                $dispatch = new $class->name();
                $method = $class->method ? $class->method : 'Index';
                if ((int)method_exists($dispatch, $method)) {
//                        echo call_user_func(array($dispatch, $method)/*, $_GET*/);
                    echo call_user_func(array($dispatch, $method));
                    exit(0);
                } else {
                    throw new Exception("{$class->name}: 控制器方法 {$method} 不存在!");
                }
            }
        }
        // 如果是PC后端请求，找不到路由就直接404
        if ($is_pc_admin) $this->base_request->getResponse()->noRouter();
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
        if (is_bool(strpos($filename, \M\Framework\View\Data\DataInterface::dir))) $this->base_request->getResponse()->noRouter();
        if (is_file($filename)) {
            $filename_arr = explode('.', $filename);
            $file_ext = end($filename_arr);
            if ($file_ext == 'css' || $file_ext == 'less' || $file_ext == 'sass') {
                $mime_type = 'text/css';
            } else {
                $fi = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $fi->file($filename);
            }
            header('Content-Type:' . $mime_type);
            return readfile($filename);
        };
        return false;
    }
}