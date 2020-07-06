<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/4
 * 时间：17:07
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Router\Helper;


use M\Framework\App\Etc;
use M\Framework\FileSystem\Io\File;
use M\Framework\Register\Router\Data\DataInterface;

class Data
{
    /**
     * @DESC         |更新模块数据
     *
     * 参数区：
     *
     * @param array $modules
     * @throws \M\Framework\App\Exception
     */
    function updateModules(array &$modules)
    {
        $file = new File();
        $file->open(Etc::path_MODULES_FILE, $file::mode_w_add);
        $text = '<?php return ' . var_export($modules, true) . ';';
        $file->write($text);
        $file->close();
    }

    /**
     * @DESC         |更新模块数据
     *
     * 参数区：
     *
     * @param array $routers
     * @throws \M\Framework\App\Exception
     */
    function updatePcRouters(array &$routers)
    {
        $file = new File();
        $file->open(Etc::path_PC_ROUTER_FILE, $file::mode_w_add);
        $text = '<?php return ' . var_export($routers, true) . ';';
        $file->write($text);
        $file->close();
    }

    /**
     * @DESC         |更新模块数据
     *
     * 参数区：
     *
     * @param array $api
     * @throws \M\Framework\App\Exception
     */
    function updateApiRouters(array &$api)
    {
        $routers[$api['router']] = $api['rule'];
        if (is_file(Etc::path_API_ROUTER_FILE)) {
            $file_routers = require Etc::path_API_ROUTER_FILE;
            $routers = array_merge($file_routers,$routers);
        }
        $file = new File();
        $file->open(Etc::path_API_ROUTER_FILE, $file::mode_w_add);
        $text = '<?php return ' . var_export($routers, true) . ';';
        $file->write($text);
        $file->close();
    }
}