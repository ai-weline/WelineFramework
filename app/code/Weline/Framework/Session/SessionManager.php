<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Session;

use Weline\Framework\App\Env;

class SessionManager
{
    const driver_NAMESPACE = Env::framework_name . '\\Framework\\Session\\Driver\\';

    private static SessionManager $instance;

    private array $config;

    private function __clone()
    {
    }

    private function __construct()
    {
        $this->config = Env::getInstance()->getConfig('session');
    }

    /**
     * @DESC         |获取实例
     *
     * 参数区：
     *
     * @return SessionManager
     */
    public static function getInstance(): SessionManager
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     * @param string $driver
     * @param string $area
     * @return SessionInterface
     */
    public function create(string $driver = ''): SessionInterface
    {
        if (empty($driver) && isset($this->config['default'])) {
            $driver = $this->config['default'];
        }
        $driver_class = self::driver_NAMESPACE . ucfirst($driver);

        return new $driver_class($this->config['drivers'][$driver]);
    }
}
