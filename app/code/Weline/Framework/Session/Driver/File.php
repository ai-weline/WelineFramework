<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
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
        # 会话启用 但是不存在时 新建会话
        if (session_status() !== PHP_SESSION_NONE) {
            if (!is_dir($this->sessionPath)) {
                mkdir($this->sessionPath, 0700);
            }
            session_set_cookie_params(24 * 3600);
            session_save_path($this->sessionPath);
            session_start();
            p(session_id());
            $_SESSION = array();
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
    public function set($name, $value): bool
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
     * @return mixed
     */
    public function get($name): mixed
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
    public function del($name): bool
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
    public function des(): bool
    {
        $_SESSION = [];
        return session_destroy();
    }

    public function open(): bool
    {
        return true;
    }

    public function gc(int $sessMaxLifeTime): bool
    {
        ini_set('session.gc_maxlifetime', $sessMaxLifeTime);
        return true;
    }
}
