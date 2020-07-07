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

    private function __clone()
    {

    }

    private function __construct()
    {
        $this->_etc = Etc::getInstance();
        $this->base_request = BaseRequest::getInstance();
        $this->request_area = $this->base_request->getRequestArea();
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
        $url = trim($this->base_request->getUrl(), '/');
        if (empty($url)) $url = 'index/index';// 找不到则访问默认控制器
        if ($this->request_area === \M\Framework\Router\DataInterface::area_BACKEND)
            $url = str_replace(Etc::getInstance()->getConfig('admin', ''), '', $url);
        $url = trim($url, '/');
        // API
        $this->Api($url);
        // PC
        $this->Pc($url);
        // 静态资源
        if ($this->StaticFile($url)) return;
        // 开发模式
        if (DEBUG) throw new Exception('未知的路由！');
        // 404
        $this->noRoute();
    }

    /**
     * @DESC         |api路由
     *
     * 参数区：
     *
     * @param string $url
     * @throws Exception
     */
    public function Api(string &$url)
    {
        // 检测api路由
        if (file_exists(Etc::path_API_ROUTER_FILE)) {
            $routers = include Etc::path_API_ROUTER_FILE;
            foreach ($routers as $router => $class) {
                $class = json_decode(json_encode($class['class']));
                $router = strstr($router, '::', true);
                $router = trim($router, '/');
                if ($url === $router && $class->request_method === $this->base_request->getMethod()) {
                    $dispatch = new $class->name();
                    $method = $class->method;
                    if ((int)method_exists($dispatch, $method)) {
//                        echo call_user_func(array($dispatch, $method), $this->getParams());
                        echo call_user_func(array($dispatch, $method));
                        exit(0);
                    } else {
                        throw new Exception("{$class->name}: 控制器方法 {$method} 不存在!");
                    }
                }
            }
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
    public function Pc(string &$url)
    {
        // 检测api路由
        if (file_exists(Etc::path_PC_ROUTER_FILE)) {
            $routers = include Etc::path_PC_ROUTER_FILE;
            foreach ($routers as $router => $class) {
                $class = json_decode(json_encode($class['class']));
                $router = trim($router, '/');
                // 是否无控制方法
                $url_no_ctl = count(explode(DIRECTORY_SEPARATOR, $url)) == 1;
                $url = ($url_no_ctl) ? $url . DIRECTORY_SEPARATOR . 'index' : $url;
                if ($url === $router) {
                    // 检测注册方法
                    $dispatch = new $class->name();
                    $method = ($url_no_ctl) ? 'index' : $class->method;
                    if ((int)method_exists($dispatch, $method)) {
//                        echo call_user_func(array($dispatch, $method)/*, $_GET*/);
                        echo call_user_func(array($dispatch, $method));
                        exit(0);
                    } else {
                        throw new Exception("{$class->name}: 控制器方法 {$method} 不存在!");
                    }
                }
            }
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

    /**
     * @DESC         | 无路由
     *
     * 参数区：
     *
     */
    function noRoute()
    {
        http_response_code(404);
        exit(0);
    }
}