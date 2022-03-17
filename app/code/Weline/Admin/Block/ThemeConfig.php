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
    const caches = [
        'backend_body_layouts_attributes',
        'backend_theme_model'
    ];
    private AdminSession $adminSession;
    private AdminUserConfig $adminUserConfig;

    function __construct(AdminUserConfig $adminUserConfig, AdminSession $adminSession, array $data = [])
    {
        parent::__construct($data);
        $this->adminSession    = $adminSession;
        $this->adminUserConfig = $adminUserConfig;
    }

    function __init()
    {
        $this->adminUserConfig = $this->adminSession->getLoginUserID() ? $this->adminUserConfig->load($this->adminSession->getLoginUserID()) : $this->adminUserConfig;
        if (!$this->adminUserConfig->getAdminUserId()) {
            $this->adminUserConfig->setId($this->adminSession->getLoginUserID());
        }
    }

    function getThemeConfig(string $key = '')
    {
        if ($data = $this->_cache->get($key)) {
            return $data;
        }
        if ($key) {
            if ($data = $this->adminSession->getData($key)) {
                return $data;
            }
            $data = $this->adminUserConfig->getConfig($key);
            $this->adminSession->setData($key, $data);
        } else {
            $data = $this->adminUserConfig->getOriginConfig();
        }
        $this->_cache->set($key, $data);
        return $data;
    }

    function getThemeModel()
    {
        $cache_key = 'backend_theme_model';
        if ($data = $this->_cache->get($cache_key)) {
            return $data;
        }
        $data = '';
        if ($this->getThemeConfig('dark-mode-switch')) {
            $data = 'dark';
        } elseif ($this->getThemeConfig('rtl-mode-switch')) {
            $data = 'rtl';
        } elseif ($this->getThemeConfig('light-mode-switch')) {
            $data = '';
        }
        $this->_cache->set($cache_key, $data);
        return $data;
    }

    function setThemeConfig(string|array $key, mixed $value = ''): static
    {
        $this->adminUserConfig->addConfig($key, $value)->forceCheck()->save();
        return $this;
    }


    public string $cache_key_backend_body_attributes = 'backend_body_layouts_attributes';


    function getLayouts()
    {
        if ($data = $this->_cache->get($this->cache_key_backend_body_attributes)) {
            return $data;
        }
        $body_attributes     = is_array($this->adminUserConfig->getConfig('layouts')) ? $this->adminUserConfig->getConfig('layouts') : [];
        $body_attributes_str = '';
        foreach ($body_attributes as $attribute => $value) {
            if (is_string($value)) $body_attributes_str .= "$attribute=\"$value\" ";
        }
        $this->_cache->set($this->cache_key_backend_body_attributes, $body_attributes_str);
        return $body_attributes_str;
    }
}