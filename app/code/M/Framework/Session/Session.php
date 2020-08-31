<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/7
 * 时间：22:47
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Session;


class Session
{
    const login_KEY = 'M_USER';
    private SessionInterface $session;

    function __construct()
    {
        $this->session = SessionManager::getInstance()->create();
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return SessionInterface
     */
    function getSession(): SessionInterface
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
    function isLogin()
    {
        return $this->session->get(self::login_KEY);
    }

    function login($user)
    {
        return $this->session->set(self::login_KEY, $user);
    }

    function loginOut()
    {
        return $this->session->des();
    }
}