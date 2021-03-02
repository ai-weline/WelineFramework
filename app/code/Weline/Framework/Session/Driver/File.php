<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Session\Driver;

class File extends AbstractSessionDriverHandle
{
    private string $sessionPath;

    /**
     * File 初始函数...
     * @param array $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->sessionPath = BP . $config['path'] ?? BP . 'var/session/';
        if (session_status() !== 2) {
            if (! is_dir($this->sessionPath)) {
                mkdir($this->sessionPath, 0700);
            }
            ini_set('session.save_handler', DriverInterface::driver_FILE);
            ini_set('session.save_path', $this->sessionPath);
            session_start();
        }
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
    public function set($name, $value)
    {
        $_SESSION[$name] = $value;
        if ($_SESSION[$name]) {
            return true;
        }

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
    public function get($name)
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }

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
    public function del($name)
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
    public function des()
    {
        $_SESSION = [];

        return session_destroy();
    }

    public function open()
    {
        return true;
    }

    public function gc(int $sessMaxLifeTime)
    {
        ini_set('session.gc_maxlifetime', $sessMaxLifeTime);

        return true;
    }
}
