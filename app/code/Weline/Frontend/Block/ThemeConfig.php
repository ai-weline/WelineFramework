<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Frontend\Block;

use Weline\Frontend\Model\UserConfig;
use Weline\Frontend\Session\UserSession;

class ThemeConfig extends \Weline\Framework\View\Block
{
    private UserSession $userSession;
    private UserConfig $userConfig;

    public function __construct(UserConfig $userConfig, UserSession $frontendSession, array $data = [])
    {
        parent::__construct($data);
        $this->userSession    = $frontendSession;
        $this->userConfig = $userConfig;
    }

    public function __init()
    {
        $this->userConfig = $this->userSession->getLoginUserID() ? $this->userConfig->load($this->userSession->getLoginUserID()) : $this->userConfig;
        if (!$this->userConfig->getAdminUserId()) {
            $this->userConfig->setId($this->userSession->getLoginUserID());
        }
    }

    public function getThemeConfig(string $key = '')
    {
        if ($key) {
            if ($data = $this->userSession->getData($key)) {
                return $data;
            }
            $data = $this->userConfig->getConfig($key);
        } else {
            $key = 'theme_config';
            if ($data = $this->userSession->getData($key)) {
                return $data;
            }
            $data = $this->userConfig->getOriginConfig();
        }
        $this->userSession->setData($key, $data);
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
                $this->userSession->setData($i, $v);
            }
        } else {
            $this->userSession->setData($key, $value);
        }
        $this->userConfig->addConfig($key, $value)->forceCheck()->save();
        return $this;
    }


    public string $cache_key_backend_body_attributes = 'backend_body_layouts_attributes';


    public function getLayouts()
    {
        $body_attributes     = is_array($this->userConfig->getConfig('layouts')) ? $this->userConfig->getConfig('layouts') : [];
        $body_attributes_str = '';
        foreach ($body_attributes as $attribute => $value) {
            if (is_string($value)) {
                $body_attributes_str .= "$attribute=\"$value\" ";
            }
        }
        $this->userSession->setData($this->cache_key_backend_body_attributes, $body_attributes_str);
        return $body_attributes_str;
    }
}
