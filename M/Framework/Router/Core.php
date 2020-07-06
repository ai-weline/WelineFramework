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
use M\Framework\Http\Request;

class Core extends Request\BaseRequest
{
    const dir_static = 'static';
    private ?Etc $_etc;
    private static Core $instance;

    private function __clone()
    {

    }

    private function __construct()
    {
        $this->_etc = Etc::getInstance();
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
        // Api
        $this->api();
        // PC
        $this->pc();
        // 静态资源
        if ($this->staticFile()) return;
        // 开发模式
        if (DEBUG) throw new Exception('未知的路由！');
        // 404
        http_response_code(404);
        exit(0);
    }

    /**
     * @DESC         |api路由
     *
     * 参数区：
     *
     * @throws Exception
     */
    public function api()
    {
        // 检测api路由
        if (file_exists(Etc::path_API_ROUTER_FILE)) {
            $routers = include Etc::path_API_ROUTER_FILE;
            foreach ($routers as $router => $class) {
                $class = json_decode(json_encode($class['class']));
                $router = strstr($router, '::', true);
                $router = trim($router, '/');
                $url = strtolower(trim($this->getUrl(), '/'));
                if ($url === $router && $class->request_method === $this->getMethod()) {
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
     * @throws Exception
     */
    public function pc()
    {
        // 检测api路由
        if (file_exists(Etc::path_PC_ROUTER_FILE)) {
            $routers = include Etc::path_PC_ROUTER_FILE;
            foreach ($routers as $router => $class) {
                $class = json_decode(json_encode($class['class']));
                $router = trim($router, '/');
                $url = strtolower(trim($this->getUrl(), DIRECTORY_SEPARATOR));
                // 是否无控制方法
                $url_no_ctl = count(explode(DIRECTORY_SEPARATOR, $url)) == 1;
                $url = ($url_no_ctl) ? $url . DIRECTORY_SEPARATOR . 'index' : $url;
                if ($url === $router) {
                    // 检测注册方法
                    $dispatch = new $class->name();
                    $method = ($url_no_ctl) ? 'index' : $class->method;
                    if ((int)method_exists($dispatch, $method)) {
//                        echo call_user_func(array($dispatch, $method), $this->_request->getParams());
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
     * @return bool|mixed
     */
    public function staticFile()
    {
        $filename = APP_PATH . trim($this->getUrl(), DIRECTORY_SEPARATOR);
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