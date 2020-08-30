<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/20
 * 时间：23:37
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Router;


use M\Framework\App\Env;
use M\Framework\App\Exception;
use M\Framework\Console\ConsoleException;
use M\Framework\FileSystem\Io\File;
use M\Framework\Http\RequestInterface;
use M\Framework\Register\Router\Data\DataInterface;
use M\Framework\Router\Helper\Data;

class Handle
{
    const path_backend_PC = Env::path_BACKEND_PC_ROUTER_FILE;
    const path_frontend_PC = Env::path_FRONTEND_PC_ROUTER_FILE;
    const path_fronted_API = Env::path_FRONTEND_REST_API_ROUTER_FILE;
    const path_backend_API = Env::path_BACKEND_REST_API_ROUTER_FILE;
    private Data $helper;
    private array  $modules;

    function __construct()
    {
        $this->helper = new Data();
        $this->modules = Env::getInstance()->getModuleList();
    }

    /**
     * @DESC         |路由注册
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array $routerParam
     * @return array|mixed
     * @throws \M\Framework\App\Exception
     */
    function register(array $routerParam)
    {

        switch ($routerParam['type']) {
            case DataInterface::type_API:
                $path = '';
                if ($routerParam['area'] == \M\Framework\Controller\Data\DataInterface::type_api_REST_FRONTEND ) {
                    $path = self::path_fronted_API;
                } elseif ($routerParam['area'] == \M\Framework\Controller\Data\DataInterface::type_api_BACKEND) {
                    $path = self::path_backend_API;
                };
                if ($path) {
                    $router = array(
                        'module' => $routerParam['module'],
                        'class' => array(
                            'area' => $routerParam['area'],
                            'name' => $routerParam['class'],
                            'method' => $routerParam['method'],
                            'request_method' => $routerParam['request_method']
                        ),
                    );
                    // 如果模块已安装
                    $api = ['router' => $routerParam['router'], 'rule' => $router];
                    // 更新api路由
                    $this->helper->updateApiRouters($path, $api);
                    return $api;
                }
                break;
            case DataInterface::type_PC:
                $path = '';
                if ($routerParam['area'] == \M\Framework\Controller\Data\DataInterface::type_pc_FRONTEND) {
                    $path = self::path_frontend_PC;
                } elseif ($routerParam['area'] == \M\Framework\Controller\Data\DataInterface::type_pc_BACKEND) {
                    $path = self::path_backend_PC;
                };
                if ($path) {
                    $routers = [];
                    if (is_file($path)) $routers = require $path;
                    $router = array(
                        'module' => $routerParam['module'],
                        'class' => array(
                            'area' => $routerParam['area'],
                            'name' => $routerParam['class'],
                            'method' => $routerParam['method']
                        ),
                    );
                    $routers[$routerParam['router']] = $router;

                    // 写入路由文件
                    $this->helper->updatePcRouters($path, $routers);
                }
                break;
            default:
                throw new ConsoleException('未知的路由类型：' . $routerParam['type']);

        }

    }
}