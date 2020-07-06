<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/6
 * 时间：15:13
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Session\Driver;


use M\Framework\Session\SessionInterface;

class File implements SessionInterface, DriverInterface
{
    private string $session_path;

    public function __construct(array $config)
    {
        $this->session_path = BP . $config['path'] ?? BP . 'var/session/';
        if (!is_dir($this->session_path)) mkdir($this->session_path, 0770);
        ini_set('session.save_path', $this->session_path);
        session_start();
    }

    function set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    function get($name)
    {
        if (isset($_SESSION[$name]))
            return $_SESSION[$name];
        else
            return false;
    }

    function del($name)
    {
        unset($_SESSION[$name]);
    }

    function des()
    {
        $_SESSION = array();
        session_destroy();
    }
}