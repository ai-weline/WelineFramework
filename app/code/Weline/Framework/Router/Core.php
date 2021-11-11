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
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Router\Cache\RouterCache;

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

    private CacheInterface $cache;

    protected array $router;
    protected string $_router_cache_key;

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
        $this->cache = ObjectManager::getInstance(RouterCache::class . 'Factory');
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
        # 获取URL
        $url = $this->processUrl();
        $this->_router_cache_key = $this->area_router . $url;
        if ($router = $this->cache->get($this->_router_cache_key)) {
            $this->router = $router;
            return $this->route();
        }

        if ($pc_result = $this->Pc($url)) {
            return $pc_result;
        }
        // API
        if ($api_result = $this->Api($url)) {
            return $api_result;
        }
        // 非开发模式（匹配不到任何路由将报错）
        if (!DEV) {
            $this->request->getResponse()->noRouter();
        } else {
            // 开发模式(静态资源可访问app本地静态资源)
            $static = $this->StaticFile($url);
            if ($static) return $static;
            throw new Exception('未知的路由！');
        }
        return '';
    }

    function processUrl()
    {
        // 读取url
        $url = $this->request->getUrlPath();
        $url_cache_key = 'url_cache_key_' . $url;
        if (!DEV && $cached_url = $this->cache->get($url_cache_key)) {
            $url = $cached_url;
        } else {
            // 前后台路由处理
            if ($this->is_admin) {
                if ($this->area_router === $this->_etc->getConfig('admin', '')) {
                    $url = str_replace($this->area_router, '', $url);
                    $url = trim($url, self::url_path_split);
                    if (!strstr($url, self::url_path_split)) {
                        $url .= self::default_index_url;
                    }
                } elseif ($this->area_router === $this->_etc->getConfig('api_admin', '')) {
                    $url = str_replace($this->area_router, '', $url);
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
            $url = strtolower(trim($url, self::url_path_split));
            $this->cache->set($url_cache_key, $url);
        }
        return $url;
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
                $this->router = $routers[$url . $method] ?? $routers[$url . '/index' . $method];
                # 缓存路由结果
                $this->cache->set($this->_router_cache_key, $this->router);
                return $this->route();
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
        $is_pc_admin = $this->request_area === \Weline\Framework\Controller\Data\DataInterface::type_pc_BACKEND;
        // 检测api路由区域
        if ($is_pc_admin) {
            $router_filepath = Env::path_BACKEND_PC_ROUTER_FILE;
        } else {
            $router_filepath = Env::path_FRONTEND_PC_ROUTER_FILE;
        }
        $url_class_method_cache_key = 'url_class_method_cache_key';
        $class_method = $this->cache->get($url_class_method_cache_key);
        if (is_file($router_filepath)) {
            $routers = include $router_filepath;
            if (isset($routers[$url]) || isset($routers[$url . '/index']) || isset($routers[$url . self::default_index_url])) {
                $this->router = $routers[$url] ?? $routers[$url . '/index'] ?? $routers[$url . self::default_index_url];
                # 缓存路由结果
                $this->cache->set($this->_router_cache_key, $this->router);
//                list($dispatch, $method) = $this->getController($router);
//                if (method_exists($dispatch, $method)) {
//                    exit(call_user_func([$dispatch, $method], $this->request->getParams()));
//                }
                $this->route();
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
        header ("Cache-Control: max-age=3600");
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


    function getController(array $router): array
    {
//        $controller_cache_key = 'controller_cache_key_' . implode(',', $router);
//        $controller = $this->cache->get($controller_cache_key);
//        if($controller){
//
//        }else{
//            $class = json_decode(json_encode($router['class']));
//            $this->request->setRouter($router);
//            // 检测注册方法
//            /**@var \Weline\Framework\Controller\Core $dispatch */
//            $dispatch = ObjectManager::getInstance($class->name);
//            $dispatch->setModuleInfo($router);
//        }
        $class = json_decode(json_encode($router['class']));
        // 检测注册方法
        /**@var \Weline\Framework\Controller\Core $dispatch */
        $dispatch = ObjectManager::getInstance($class->name);
        $dispatch->setModuleInfo($router);
        $method = $class->method ?: 'index';
        # 检测控制器方法
        if (!method_exists($dispatch, $method)) {
            throw new Exception("{$class->name}: 控制器方法 {$method} 不存在!");
        }
        return [$dispatch, $method];
    }

    function route(): string
    {
        $this->request->setRouter($this->router);
        list($dispatch, $method) = $this->getController($this->router);
        if (method_exists($dispatch, $method)) {
            exit(call_user_func([$dispatch, $method], $this->request->getParams()));
        }
        return '';
    }
}
