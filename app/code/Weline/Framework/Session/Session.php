<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Session;

use Weline\Framework\App\Env;
use Weline\Framework\Session\Driver\SessionDriverHandlerInterface;

class Session implements SessionInterface
{
    const login_KEY = 'WL_USER';

    private ?SessionDriverHandlerInterface $session = null;

    public function __construct()
    {
        if (!isset($this->session)) {
            $this->__init();
        }
    }


    function __init()
    {
        if (isset($_SERVER['REQUEST_URI']) && !isset($this->session)) {
            $type = 'frontend';
            $identity_path = '/';
            if (strstr($_SERVER['REQUEST_URI'], Env::getInstance()->getConfig('admin'))) {
                $identity_path .= Env::getInstance()->getConfig('admin');
                $type = 'backend';
            } elseif (strstr($_SERVER['REQUEST_URI'], Env::getInstance()->getConfig('api_admin'))) {
                $identity_path .= Env::getInstance()->getConfig('api_admin');
                $type = 'api';
            }
            if (session_status() !== PHP_SESSION_ACTIVE) {
                session_set_cookie_params(3600, $identity_path);
            }
            $this->session = SessionManager::getInstance()->create();
            $this->setType($type)->setData('path', $identity_path);
        }
    }

    /**
     * @DESC          # 设置session值
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/10/22 21:45
     * 参数区：
     * @param string $name
     * @param string $value
     * @return SessionDriverHandlerInterface
     */
    function setData(string $name, mixed $value): SessionDriverHandlerInterface
    {
        $this->session->set($name, $value);
        return $this->session;
    }

    /**
     * @DESC          # 获取session值
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/10/22 21:45
     * 参数区：
     * @param string $name
     * @return string
     */
    function getData(string $name): string
    {
        return $this->session->get($name);
    }

    /**
     * @DESC          # 获取session值
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/10/22 21:45
     * 参数区：
     * @param string $name
     * @return string
     */
    function addData(string $name, string $value): string
    {
        return $this->session->set($name, $this->session->get($name) . $value);
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return SessionDriverHandlerInterface
     */
    public function getOriginSession(): SessionDriverHandlerInterface
    {
        return $this->session;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return mixed
     */
    public function isLogin(): bool
    {
        return (bool)$this->session->get(self::login_KEY);
    }

    public function login(mixed $user)
    {
        return $this->session->set(self::login_KEY, $user);
    }

    public function logout(): bool
    {
        return $this->session->delete(self::login_KEY);
    }

    function getType(): string
    {
        return $this->session->get('type');
    }

    function setType(string $type): static
    {
        $this->session->set('type', $type);
        return $this;
    }

    function isBackend(): bool
    {
        return $this->getType() === 'backend';
    }

    function isApi(): bool
    {
        return $this->getType() === 'api';
    }

    function isFrontend(): bool
    {
        return $this->getType() === 'frontend';
    }

    function destroy()
    {
        return $this->session->destroy();
    }

    function delete(string $name)
    {
        if (isset($_SESSION[$name])) {
            unset($_SESSION[$name]);
            return true;
        }
        return false;
    }
}
