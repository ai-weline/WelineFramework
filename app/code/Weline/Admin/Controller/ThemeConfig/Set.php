<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller\ThemeConfig;

use Weline\Admin\Block\ThemeConfig;
use Weline\Admin\Model\AdminUserConfig;
use Weline\Admin\Session\AdminSession;

class Set extends \Weline\Admin\Controller\BaseController
{
    private ThemeConfig $themeConfig;
    private AdminSession $adminSession;

    function __construct(
        ThemeConfig  $themeConfig,
        AdminSession $adminSession
    )
    {

        $this->themeConfig  = $themeConfig;
        $this->adminSession = $adminSession;
    }

    function postIndex(): bool|string
    {
        # 释放缓存
        foreach ($this->themeConfig::caches as $cach) {
            $this->themeConfig->_cache->set($cach, false);
        }
        $data = json_decode($this->_request->getBodyParams(), true);
        try {
            $old_layout = $this->themeConfig->getThemeConfig('layouts');
            if (isset($data['layouts'])) $data['layouts'] = array_merge($old_layout, $data['layouts']);
            $this->themeConfig->setThemeConfig($data);
            foreach ($data as $key => $datum) {
                $this->adminSession->setData($key, $datum);
            }
            return json_encode($this->success());
        } catch (\Exception $exception) {
            return json_encode($this->exception($exception));
        }
    }
}