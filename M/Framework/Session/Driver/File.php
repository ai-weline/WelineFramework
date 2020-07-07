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


class File extends AbstractSessionDriverHandle
{
    private string $sessionPath;

    /**
     * File 初始函数...
     * @param array $config
     */
    function __construct(array $config)
    {
        parent::__construct($config);
        $this->sessionPath = BP . $config['path'] ?? BP . 'var/session/';
        if (!is_dir($this->sessionPath)) mkdir($this->sessionPath, 0700);
        ini_set('session.save_handler', 'files');
        ini_set('session.save_path', $this->sessionPath);
        session_start();
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param $name
     * @param $value
     * @return bool
     */
    function set($name, $value)
    {
        $_SESSION[$name] = $value;
        if ($_SESSION[$name]) return true;
        return false;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param $name
     * @return bool|mixed
     */
    function get($name)
    {
        if (isset($_SESSION[$name]))
            return $_SESSION[$name];
        else
            return false;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param $name
     * @return bool
     */
    function del($name)
    {
        unset($_SESSION[$name]);
        return true;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return bool
     */
    function des()
    {
        $_SESSION = array();
        return session_destroy();
    }

    function open()
    {
        return true;
    }

    function gc(int $sessMaxLifeTime)
    {
        ini_set("session.gc_maxlifetime", $sessMaxLifeTime);
        return true;
    }
}