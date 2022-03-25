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
use Weline\Admin\Session\AdminSession;

class Set extends \Weline\Admin\Controller\BaseController
{
    private ThemeConfig $themeConfig;

    public function __construct(
        ThemeConfig $themeConfig,
    ) {
        $this->themeConfig = $themeConfig;
    }

    public function postIndex(): bool|string
    {
        $data = json_decode($this->_request->getBodyParams(), true);
        try {
            $old_layout = $this->themeConfig->getThemeConfig('layouts');
            if (isset($data['layouts']) && is_array($data['layouts']) && is_array($old_layout)) {
                $data['layouts'] = array_merge($old_layout, $data['layouts']);
            }
            $this->themeConfig->setThemeConfig($data);
            $this->themeConfig->setData('backend_body_layouts_attributes', false);
            return json_encode($this->success());
        } catch (\Exception $exception) {
            return json_encode($this->exception($exception));
        }
    }
}
