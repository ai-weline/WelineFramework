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
use Weline\Backend\Cache\BackendCache;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Manager\ObjectManager;

class ThemeConfig extends \Weline\Framework\View\Block
{
    private AdminSession $adminSession;
    private AdminUserConfig $adminUserConfig;

    function __construct(AdminUserConfig $adminUserConfig, AdminSession $adminSession, array $data = [])
    {
        parent::__construct($data);
        $this->adminSession    = $adminSession;
        $this->adminUserConfig = $adminUserConfig->load($adminSession->getLoginUserID());
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

    function setThemeConfig(string|array $key, mixed $value = ''): static
    {
        $this->adminUserConfig->addConfig($key, $value)->forceCheck()->save();
        return $this;
    }

    function getThemeConfigJson()
    {
        $cache_key = 'backend_getThemeConfigJson';
        if ($data = $this->_cache->get($cache_key)) {
            return $data;
        }
        $json = $this->adminUserConfig->getOriginConfig();
        $this->_cache->set($cache_key, $json);
        return $json;
    }

    public string $cache_key_backend_body_attributes = 'backend_body_layouts_attributes';

    function addLayouts(string|array $attribute, string $value = ''): bool
    {

        $body_attributes = is_array($this->adminUserConfig->getConfig('layouts')) ?$this->adminUserConfig->getConfig('layouts'): [];
        if (is_array($attribute)) {
            $body_attributes = array_merge($body_attributes, $attribute);
        } else {
            $body_attributes[$attribute] = $value;
        }
        $body_attributes_str = implode(' ', $body_attributes);
        $this->_cache->set($this->cache_key_backend_body_attributes, $body_attributes_str);
        return $this->adminUserConfig->addConfig('layouts', $body_attributes_str)->save();
    }

    function getLayouts()
    {
        if ($data = $this->_cache->get($this->cache_key_backend_body_attributes)) {
            return $data;
        }
        $body_attributes     = is_array($this->adminUserConfig->getConfig('layouts')) ?$this->adminUserConfig->getConfig('layouts'): [];
        $body_attributes_str = implode(' ', $body_attributes);
        $this->_cache->set($this->cache_key_backend_body_attributes, $body_attributes_str);
        return $body_attributes_str;
    }
}