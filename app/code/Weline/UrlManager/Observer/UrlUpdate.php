<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/9/15 21:46:12
 */

namespace Weline\UrlManager\Observer;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Event\Event;
use Weline\Framework\Exception\Core;
use Weline\ModuleManager\Model\Module;
use Weline\UrlManager\Model\UrlManager;

use function PHPUnit\Framework\throwException;

class UrlUpdate implements \Weline\Framework\Event\ObserverInterface
{
    private Module $module;
    private UrlManager $urlManager;

    public function __construct(
        Module     $module,
        UrlManager $urlManager
    )
    {
        $this->module     = $module;
        $this->urlManager = $urlManager;
    }

    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        # 读取前端PC url存放位置更新到数据库中
        if (is_file(Env::path_FRONTEND_PC_ROUTER_FILE)) {
            $type             = 'frontend_pc';
            $frontend_pc_urls = include Env::path_FRONTEND_PC_ROUTER_FILE;
            if (is_array($frontend_pc_urls))
                foreach ($frontend_pc_urls as $path => $frontend_pc_url) {
                    $this->module->recovery();
                    $module_id = $this->module->load('name', $frontend_pc_url['module'])->getId();
                    if (!$module_id) {
                        throw new \Exception(__('模型不存在！'));
                    }
                    $this->urlManager->recovery();
                    $this->urlManager
                        ->setData('module_id', $module_id)
                        ->setData('path', $path)
                        ->setData('identify', md5($path . $type), true)
                        ->setData('data', json_encode($frontend_pc_url))
                        ->setData('type', $type)
                        ->save();
                }
        }

        # 读取前端REST api存放位置更新到数据库中
        if (is_file(Env::path_FRONTEND_REST_API_ROUTER_FILE)) {
            $frontend_api_urls = include Env::path_FRONTEND_REST_API_ROUTER_FILE;
            $type              = 'frontend_rest';
            if (is_array($frontend_api_urls))
                foreach ($frontend_api_urls as $path => $frontend_api_url) {
                    $this->module->recovery();
                    $module_id = $this->module->load('name', $frontend_api_url['module'])->getId();
                    if (!$module_id) {
                        throw new \Exception(__('模型不存在！'));
                    }
                    $this->urlManager->recovery();
                    $this->urlManager
                        ->setData('module_id', $module_id)
                        ->setData('path', $path)
                        ->setData('identify', md5($path . $type), true)
                        ->setData('data', json_encode($frontend_api_url))
                        ->setData('type', $type)
                        ->save();
                }
        }

        # 读取后端PC url存放位置更新到数据库中
        if (is_file(Env::path_BACKEND_PC_ROUTER_FILE)) {
            $type            = 'backend_pc';
            $backend_pc_urls = include Env::path_BACKEND_PC_ROUTER_FILE;
            if (is_array($backend_pc_urls))
                foreach ($backend_pc_urls as $path => $backend_pc_url) {
                    $this->module->recovery();
                    $module_id = $this->module->load('name', $backend_pc_url['module'])->getId();
                    if (!$module_id) {
                        throw new \Exception(__('模型不存在！'));
                    }
                    $this->urlManager->recovery();
                    $this->urlManager
                        ->setData('module_id', $module_id)
                        ->setData('path', $path)
                        ->setData('identify', md5($path . $type), true)
                        ->setData('data', json_encode($backend_pc_url))
                        ->setData('type', $type)
                        ->save();
                }
        }

        # 读取后端REST api存放位置更新到数据库中
        if (is_file(Env::path_BACKEND_PC_ROUTER_FILE)) {
            $backend_api_urls = include Env::path_BACKEND_PC_ROUTER_FILE;
            $type             = 'backend_rest';
            if (is_array($backend_api_urls)) {
                foreach ($backend_api_urls as $path => $backend_api_url) {
                    $this->module->recovery();
                    $module_id = $this->module->load('name', $backend_api_url['module'])->getId();
                    if (!$module_id) {
                        throw new \Exception(__('模型不存在！'));
                    }
                    $this->urlManager->recovery();
                    $this->urlManager
                        ->setData('module_id', $module_id)
                        ->setData('path', $path)
                        ->setData('identify', md5($path . $type), true)
                        ->setData('data', json_encode($backend_api_url))
                        ->setData('type', $type)
                        ->save();
                }
            }
        }

    }
}
