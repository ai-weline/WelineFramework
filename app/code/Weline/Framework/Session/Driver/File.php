<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Session\Driver;

class File implements SessionDriverHandlerInterface
{
    private function clone(){}
    private string $sessionPath;

    /**
     * File 初始函数...
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->sessionPath = isset($config['path']) ? BP . str_replace('/', DIRECTORY_SEPARATOR, $config['path']) : BP . 'var' . DIRECTORY_SEPARATOR . 'session' . DIRECTORY_SEPARATOR;
        # 会话启用 但是不存在时 新建会话
        if (session_status() !== PHP_SESSION_ACTIVE) {
            if (!is_dir($this->sessionPath)) {
                mkdir($this->sessionPath, 0700);
            }
            session_save_path($this->sessionPath);
            ini_set('session.save_handler', 'files');
            $_SESSION = array();
        }
        session_start();
    }

    public function set($name, $value): bool
    {
        $_SESSION[$name] = $value;
        if ($_SESSION[$name]) {
            return true;
        }
        return false;
    }

    public function get($name): mixed
    {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return false;
    }

    public function delete($name): bool
    {
        unset($_SESSION[$name]);
        return true;
    }
    public function destroy(): bool
    {
        $_SESSION = [];
        return session_destroy();
    }
}
