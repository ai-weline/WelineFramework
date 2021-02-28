<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\App;

use Weline\Framework\App\Env\Modules;
use Weline\Framework\System\File\Io\File;

class Env
{
    const vendor_path = BP . 'vendor' . DIRECTORY_SEPARATOR . 'aiweline';

    const framework_name = 'Weline';

    const path_framework_generated = BP . 'generated' . DIRECTORY_SEPARATOR;

    const path_framework_generated_code = self::path_framework_generated . 'code' . DIRECTORY_SEPARATOR;

    // 路径
    const path_ENV_FILE = APP_ETC_PATH . 'env.php';

    const path_MODULES_FILE = self::path_framework_generated . 'modules.php';

    const path_COMMANDS_FILE = self::path_framework_generated . 'commands.php';

    // 路由
    const path_ROUTERS_DIR = self::path_framework_generated . 'routers' . DIRECTORY_SEPARATOR;

    const path_BACKEND_REST_API_ROUTER_FILE = self::path_ROUTERS_DIR . 'backend_rest_api.php';

    const path_BACKEND_PC_ROUTER_FILE = self::path_ROUTERS_DIR . 'backend_pc.php';

    const path_FRONTEND_REST_API_ROUTER_FILE = self::path_ROUTERS_DIR . 'frontend_rest_api.php';

    const path_FRONTEND_PC_ROUTER_FILE = self::path_ROUTERS_DIR . 'frontend_pc.php';

    const router_files_PATH = [
        self::path_BACKEND_REST_API_ROUTER_FILE,
        self::path_FRONTEND_REST_API_ROUTER_FILE,
        self::path_BACKEND_PC_ROUTER_FILE,
        self::path_FRONTEND_PC_ROUTER_FILE,
    ];

    // 翻译词典
    const path_TRANSLATE_WORDS_FILE = BP . 'generated' . DIRECTORY_SEPARATOR . 'language.php';

    // 日志
    const log_path_ERROR = 'error';

    const log_path_EXCEPTION = 'exception';

    const log_path_NOTICE = 'notice';

    const log_path_WARNING = 'warning';

    const log_path_DEBUG = 'debug';

    // 拓展目录
    const extend_dir = BP . 'extend' . DIRECTORY_SEPARATOR;

    // 插件
//    const path_

    // 变量

    /**
     * @var Env|null
     */
    private static ?Env $instance;

    public array $config;

    // 日志
    const default_LOG = [
        'error'     => BP . 'var/log/error.log',
        'exception' => BP . 'var/log/exception.log',
        'notice'    => BP . 'var/log/notice.log',
        'warning'   => BP . 'var/log/warning.log',
        'debug'     => BP . 'var/log/debug.log',
    ];

    // 缓存
    const default_CACHE = [
        'default' => 'file',
        'drivers' => [
            'file' => [
                'path' => 'var/cache/',
            ],
            'redis' => [
                'tip'      => '开发中...',
                'server'   => '127.0.0.1',
                'port'     => 6379,
                'database' => 1,
            ],
        ],
    ];

    // Session
    const default_SESSION = [
        'default' => 'file',
        'drivers' => [
            'file' => [
                'path' => 'var/session/',
            ],
            'mysql' => [
                'tip' => '开发中...',
            ],
            'redis' => [
                'tip' => '开发中...',
            ],
        ],
    ];

    /**
     * @DESC         |私有化克隆函数
     *
     * 参数区：
     */
    private function __clone()
    {
    }

    /**
     * Env 私有化 初始函数...
     */
    private function __construct()
    {
        $env_file = self::path_ENV_FILE;
        if (! is_file($env_file)) {
            $file = new File();
            $file->open($env_file, $file::mode_w_add);
            $text = '<?php return ' . var_export([
                'session' => self::default_SESSION,
                'cache'   => self::default_CACHE,
                'log'     => self::default_LOG,
            ], true) . ';?>';
            $file->write($text);
            $file->close();
        }
        $this->config = include $env_file;
    }

    /**
     * @DESC         |获得实例
     *
     * 参数区：
     *
     * @return Env
     */
    public static function getInstance()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @DESC         |获取环境参数
     *
     * 参数区：
     *
     * @param string $name
     * @param  $default
     * @return array|string
     */
    public function getConfig(string $name = '', $default = [])
    {
        if ('' === $name) {
            return $this->config;
        }

        return isset($this->config[$name]) ? $this->config[$name] : $default;
    }

    /**
     * @DESC         |设置环境参数
     *
     * 参数区：
     *
     * @param string $key
     * @param array $value
     * @return bool
     */
    public function setConfig(string $key, $value = []): bool
    {
        $config       = $this->getConfig();
        $config[$key] = $value;

        try {
            $file = new File();
            $file->open(self::path_ENV_FILE, $file::mode_w);
            $text = '<?php return ' . var_export($config, true) . ';';
            $file->write($text);
            $file->close();

            return true;
        } catch (Exception $exception) {
            return false;
        }
    }

    /**
     * @DESC         |读取log路径
     *
     * 参数区：
     *
     * @param string $type
     * @return string
     */
    public function getLogPath(string $type): string
    {
        return $this->config['log'][$type];
    }

    /**
     * @DESC         |获取数据库配置
     *
     * 参数区：
     *
     * @return array
     */
    public function getDbConfig(): array
    {
        return isset($this->config['db']) ? $this->config['db'] : [];
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return array
     */
    public function getModuleList()
    {
        return (new Modules())->getList();
    }
}
