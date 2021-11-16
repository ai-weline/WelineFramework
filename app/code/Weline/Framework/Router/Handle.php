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
use Weline\Framework\Console\ConsoleException;
use Weline\Framework\Controller\Data\DataInterface as DataInterfaceAlias;
use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Register\RegisterInterface;
use Weline\Framework\Register\Router\Data\DataInterface;
use Weline\Framework\Router\Helper\Data;

class Handle implements RegisterInterface
{
    const path_backend_PC = Env::path_BACKEND_PC_ROUTER_FILE;

    const path_frontend_PC = Env::path_FRONTEND_PC_ROUTER_FILE;

    const path_fronted_API = Env::path_FRONTEND_REST_API_ROUTER_FILE;

    const path_backend_API = Env::path_BACKEND_REST_API_ROUTER_FILE;

    private Data $helper;

    private array  $modules;

    /**
     * @var Printing
     */
    private Printing $printing;

    public function __construct()
    {
        $this->helper = new Data();
        $this->modules = Env::getInstance()->getModuleList();
        $this->printing = new Printing();
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
     * @param string $version
     * @param string $description
     * @return array|mixed
     * @throws \Weline\Framework\App\Exception
     * @throws ConsoleException
     */
    public function register($routerParam, string $version = '', string $description = '')
    {
        $controller = explode('Controller', $routerParam['class']);
        $controller = array_pop($controller);
        switch ($routerParam['type']) {
            case DataInterface::type_API:
                $path = '';
                if (in_array(DataInterfaceAlias::type_api_REST_FRONTEND, $routerParam['area'], true)) {
                    $path = self::path_fronted_API;
                    $routerParam['area'] = DataInterfaceAlias::type_api_REST_FRONTEND;
                } elseif (in_array(DataInterfaceAlias::type_api_BACKEND, $routerParam['area'], true)) {
                    $path = self::path_backend_API;
                    $routerParam['area'] = DataInterfaceAlias::type_api_BACKEND;
                } else {
                    $routerParam['area'] = self::path_fronted_API;
                }
                if ($path) {
                    $router = [
                        'module' => $routerParam['module'],
                        'module_path' => $routerParam['module_path'],
                        'class' => [
                            'area' => $routerParam['area'],
                            'name' => $routerParam['class'],
                            'controller_name' => $controller,
                            'method' => $routerParam['method'],
                            'request_method' => $routerParam['request_method'],
                        ],
                    ];
                    // 如果模块已安装
                    $api = ['router' => $routerParam['router'], 'rule' => $router];
                    // 更新api路由
                    $this->helper->updateApiRouters($path, $api);

                    return $api;
                }

                break;
            case DataInterface::type_PC:
                $path = '';
                if (in_array(DataInterfaceAlias::type_pc_FRONTEND, $routerParam['area'], true)) {
                    $path = self::path_frontend_PC;
                    $routerParam['area'] = DataInterfaceAlias::type_pc_FRONTEND;
                } elseif (in_array(DataInterfaceAlias::type_pc_BACKEND, $routerParam['area'], true)) {
                    $path = self::path_backend_PC;
                    $routerParam['area'] = DataInterfaceAlias::type_pc_BACKEND;
                }
                if ($path) {
                    $routers = [];
                    if (is_file($path)) {
                        $routers = require $path;
                    }
                    $router = [
                        'module' => $routerParam['module'],
                        'module_path' => $routerParam['module_path'],
                        'class' => [
                            'area' => $routerParam['area'],
                            'name' => $routerParam['class'],
                            'method' => $routerParam['method'],
                            'controller_name' => $controller,
                            'request_method' => $routerParam['request_method'],
                        ],
                    ];
                    $routers[$routerParam['router']] = $router;
                    // 写入路由文件
                    $this->helper->updatePcRouters($path, $routers);
                } else {
                    $this->printing->error('未知的路由区域！文件:' . $routerParam['class']);
                    if (DEV) throw new Exception(__('未知的路由区域！文件:') . $routerParam['class']);
                }

                break;
            default:
                throw new ConsoleException('未知的路由类型：' . $routerParam['type']);
        }
    }
}
