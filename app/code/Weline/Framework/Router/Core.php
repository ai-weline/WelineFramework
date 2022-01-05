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
        if (empty($this->request)) $this->request = ObjectManager::getInstance(Request::class);
        if (empty($this->cache)) $this->cache = ObjectManager::getInstance(RouterCache::class . 'Factory');
        if (empty($this->request_area)) $this->request_area = $this->request->getRequestArea();
        if (empty($this->area_router)) $this->area_router = $this->request->getAreaRouter();
        if (empty($this->_etc)) $this->_etc = Env::getInstance();
        if (empty($this->is_admin)) $this->is_admin = (bool)strstr(strtolower($this->request_area), \Weline\Framework\Router\DataInterface::area_BACKEND);
    }

    function __sleep()
    {
        return array('cache','_etc');
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
        $this->_router_cache_key = $this->area_router . $this->request->getUrlPath();
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
        if (PROD) {
            $this->request->getResponse()->noRouter();
        } else {
            // 开发模式(静态资源可访问app本地静态资源)
            $static = $this->StaticFile($url);
            if ($static) exit($static);
            http_response_code(404);
            throw new Exception('未知的路由！');
        }
        return '';
    }

    function processUrl()
    {
        // 读取url
        $url = $this->request->getUrlPath();
        $url_cache_key = 'url_cache_key_' . $url;
        if (PROD && $cached_url = $this->cache->get($url_cache_key)) {
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
            $url = trim($url, self::url_path_split);
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
                $this->router = $routers[$url . $method] ?? $routers[$url . '/index' . $method];
                # 缓存路由结果
                $this->router['type'] = 'api';
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
        $url = strtolower($url);
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
                $this->router['type'] = 'pc';
                $this->cache->set($this->_router_cache_key, $this->router);
//                list($dispatch, $method) = $this->getController($this->router);
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
    public function StaticFile(string &$url)
    {
        header("Cache-Control: max-age=3600");
        $filename = APP_CODE_PATH . trim($url, DIRECTORY_SEPARATOR);
        $filename = str_replace('/', DIRECTORY_SEPARATOR, $filename);
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
        $controller_cache_controller_key = 'controller_cache_key_' . implode('_', $router['class']).'_controller';
        $controller_cache_method_key = 'controller_cache_key_' . implode('_', $router['class']).'_method';
        $dispatch = $this->cache->get($controller_cache_controller_key);
        $dispatch_method = $this->cache->get($controller_cache_method_key);
        if ($dispatch&&$dispatch_method) {
            return [$dispatch,$dispatch_method];
        } else {
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
            $this->cache->set($controller_cache_method_key, $method);
            $this->cache->set($controller_cache_controller_key, $class->name);
            return [$class->name, $method];
        }
    }

    function route(): mixed
    {
        $this->request->setRouter($this->router);
        list($dispatch, $method) = $this->getController($this->router);
        $dispatch = ObjectManager::getInstance($dispatch);
        exit(call_user_func([$dispatch, $method], $this->request->getParams()));
    }
}
