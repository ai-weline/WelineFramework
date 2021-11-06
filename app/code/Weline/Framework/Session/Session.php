<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Session;

class Session
{
    const login_KEY = 'WL_USER';

    private ?SessionInterface $session=null;

    public function __construct()
    {
        if (!isset($this->session)) {
            $this->__init();
        }
    }

    function __init()
    {
        if (!isset($this->session)) {
            $this->session = SessionManager::getInstance()->create();
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
     * @return SessionInterface
     */
    function setData(string $name, string $value): SessionInterface
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
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return SessionInterface
     */
    public function getSession(): SessionInterface
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
    public function isLogin(): mixed
    {
        return $this->session->get(self::login_KEY);
    }

    public function login($user)
    {
        return $this->session->set(self::login_KEY, $user);
    }

    public function loginOut(): bool
    {
        return $this->session->des();
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
        return (bool)strstr($this->getType(), 'backend');
    }
}
