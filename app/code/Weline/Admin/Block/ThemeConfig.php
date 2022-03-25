<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Block;

use Weline\Admin\Model\AdminUserConfig;
use Weline\Admin\Session\AdminSession;

class ThemeConfig extends \Weline\Framework\View\Block
{
    private AdminSession $adminSession;
    private AdminUserConfig $adminUserConfig;

    public function __construct(AdminUserConfig $adminUserConfig, AdminSession $adminSession, array $data = [])
    {
        parent::__construct($data);
        $this->adminSession    = $adminSession;
        $this->adminUserConfig = $adminUserConfig;
    }

    public function __init()
    {
        $this->adminUserConfig = $this->adminSession->getLoginUserID() ? $this->adminUserConfig->load($this->adminSession->getLoginUserID()) : $this->adminUserConfig;
        if (!$this->adminUserConfig->getAdminUserId()) {
            $this->adminUserConfig->setId($this->adminSession->getLoginUserID());
        }
    }

    public function getThemeConfig(string $key = '')
    {
        if ($key) {
            if ($data = $this->adminSession->getData($key)) {
                return $data;
            }
            $data = $this->adminUserConfig->getConfig($key);
        } else {
            $key = 'theme_config';
            if ($data = $this->adminSession->getData($key)) {
                return $data;
            }
            $data = $this->adminUserConfig->getOriginConfig();
        }
        $this->adminSession->setData($key, $data);
        return $data;
    }

    public function getThemeModel()
    {
        $data = '';
        if ($this->getThemeConfig('dark-mode-switch')) {
            $data = 'dark';
        } elseif ($this->getThemeConfig('rtl-mode-switch')) {
            $data = 'rtl';
        } elseif ($this->getThemeConfig('light-mode-switch')) {
            $data = '';
        }
        return $data;
    }

    public function setThemeConfig(string|array $key, mixed $value = ''): static
    {
        if (is_array($key)) {
            foreach ($key as $i => $v) {
                $this->adminSession->setData($i, $v);
            }
        } else {
            $this->adminSession->setData($key, $value);
        }
        $this->adminUserConfig->addConfig($key, $value)->forceCheck()->save();
        return $this;
    }


    public string $cache_key_backend_body_attributes = 'backend_body_layouts_attributes';


    public function getLayouts()
    {
        $body_attributes     = is_array($this->adminUserConfig->getConfig('layouts')) ? $this->adminUserConfig->getConfig('layouts') : [];
        $body_attributes_str = '';
        foreach ($body_attributes as $attribute => $value) {
            if (is_string($value)) {
                $body_attributes_str .= "$attribute=\"$value\" ";
            }
        }
        $this->adminSession->setData($this->cache_key_backend_body_attributes, $body_attributes_str);
        return $body_attributes_str;
    }
}
