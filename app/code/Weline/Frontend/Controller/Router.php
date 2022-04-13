<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Frontend\Controller;

use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Router\Core;
use Weline\Framework\Router\RouterInterface;
use function str_contains;

class Router implements RouterInterface
{
    public static function process(DataObject &$data, Core &$router)
    {
        $flag = '/frontend/Template/Fancy';
        $path = $data->getData('path');
        if (str_contains($path, $flag)) {
            $path_arr      = explode($flag, $path);
            $function_path = array_pop($path_arr);
            if ($function_path = trim($function_path, '/')) {
                $path = $flag;
                $router->getRequest()->setData('method', $function_path);
                $data->setData('path', $path);
            }
        }
    }
}
