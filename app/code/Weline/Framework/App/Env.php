<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\App;

use Weline\Framework\App\Env\Modules;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\System\File\Io\File;

class Env extends DataObject
{
    const vendor_path   = BP . 'vendor' . DIRECTORY_SEPARATOR;

    const framework_name = 'Weline';

    const path_framework_generated = BP . 'generated' . DIRECTORY_SEPARATOR;

    const path_framework_generated_code = self::path_framework_generated . 'code' . DIRECTORY_SEPARATOR;

    # 框架模板文件位置
    const path_framework_generated_complicate = self::path_framework_generated . 'complicate' . DIRECTORY_SEPARATOR;

    // -----------------路径--------------------
    const path_ENV_FILE = APP_ETC_PATH . 'env.php';

    const path_SYSTEM_META_DATA = self::path_framework_generated . 'configs.php'; //FIXME 元数据等待开发

    const path_MODULES_FILE = APP_ETC_PATH . 'modules.php';

    const path_COMMANDS_FILE = self::path_framework_generated . 'commands.php';

    // 注册register路径

    const path_VENDOR_CODE = self::vendor_path;

    const path_CODE_DESIGN = BP . 'app' . DIRECTORY_SEPARATOR . 'design' . DIRECTORY_SEPARATOR;

    const path_LANGUAGE_PACK = BP . 'app' . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR;

    const register_FILE_PATHS = [
        'app_code'      => APP_CODE_PATH,
        'vendor_code'   => self::path_VENDOR_CODE,
        'theme_design'  => self::path_CODE_DESIGN,
        'language_pack' => self::path_LANGUAGE_PACK,
    ];

    const default_theme_DATA = [
        'id'          => 0,
        'name'        => 'default',
        'path'        => 'Weline' . DIRECTORY_SEPARATOR . 'default',
        'parent_id'   => null,
        'is_active'   => 1,
        'create_time' => '2021-04-05 16:49:58',
    ];

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

    // 生成文件的目录

    const GENERATED_DIR = BP . 'generated';

    // 编译生成文件目录
    const path_COMPLICATE_GENERATED_DIR = self::GENERATED_DIR . DIRECTORY_SEPARATOR . 'complicate' . DIRECTORY_SEPARATOR;

    // 翻译词典 目录
    const path_TRANSLATE_FILES_PATH = self::GENERATED_DIR . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR;

    const path_TRANSLATE_DEFAULT_FILE = self::GENERATED_DIR . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . 'zh_Hans_CN.php';

    const path_TRANSLATE_ALL_COLLECTIONS_WORDS_FILE = self::GENERATED_DIR . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . 'words.php';

    // 日志
    const log_path_ERROR = 'error';

    const log_path_EXCEPTION = 'exception';

    const log_path_NOTICE = 'notice';

    const log_path_WARNING = 'warning';

    const log_path_DEBUG = 'debug';

    // 拓展目录
    const extend_dir = BP . 'extend' . DIRECTORY_SEPARATOR;

    // 主题设计
    const path_THEME_DESIGN_DIR = BP . 'app' . DIRECTORY_SEPARATOR . 'design' . DIRECTORY_SEPARATOR;
    // 主题设计
    const path_UPLOAD_DIR = PUB . 'upload' . DIRECTORY_SEPARATOR;

    // 变量

    /**
     * @var Env
     */
    private static Env $instance;

    const default_CONFIG = [
        'cache'   => self::default_CACHE,
        'session' => self::default_SESSION,
        'log'     => self::default_LOG,
        'php-cs'  => true,
    ];

    // 日志
    const default_LOG = [
        'error'     => 'var' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'error.log',
        'exception' => 'var' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'exception.log',
        'notice'    => 'var' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'notice.log',
        'warning'   => 'var' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'warning.log',
        'debug'     => 'var' . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR . 'debug.log',
    ];

    // 缓存
    const default_CACHE = [
        'default' => 'file',
        'drivers' => [
            'file'  => [
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
            'file'  => [
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

    private array $config = [];

    private array $module_list = [];

    private array $hasGetConfig;

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
        try {
            $this->reload();
        } catch (Exception $e) {
            throw new Exception(__('系统加载错误：%1', $e->getMessage()));
        }
    }

    public function reload(): static
    {
        if (!is_file(self::path_ENV_FILE)) {
            $file = new File();
            $file->open(self::path_ENV_FILE, $file::mode_w_add);
            $text = '<?php return ' . var_export([], true) . ';?>';

            try {
                $file->write($text);
            } catch (Exception $e) {
                throw new Exception(__('错误：' . $e->getMessage()));
            }
            $file->close();
        }
        // 覆盖默认配置
        $this->config = array_merge(self::default_CONFIG, (array)include self::path_ENV_FILE);
        $this->setData($this->config);
        return $this;
    }

    /**
     * @DESC         |获得实例
     *
     * 参数区：
     *
     * @return Env
     */
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
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
     * @param        $default
     *
     * @return mixed
     */
    public function getConfig(string $name = '', $default = null): mixed
    {
        if (isset($this->hasGetConfig[$name])) return $this->hasGetConfig[$name];
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
     * @param        $value
     *
     * @return bool
     */
    public function setConfig(string $key, $value = null): bool
    {
        $this->hasGetConfig[$key] = $value;
        $config                   = $this->getConfig();
        $config[$key]             = $value;

        try {
            $file = new File();
            $file->open(self::path_ENV_FILE, $file::mode_w);
            $text = '<?php return ' . var_export($config, true) . ';';
            $file->write($text);
            $file->close();
            // 重置环境参数
            $this->reload();
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
     *
     * @return string
     */
    public function getLogPath(string $type): string
    {
        return BP . $this->config['log'][$type];
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
     * @DESC         |读取模块列表
     *
     * 参数区：
     *
     * @param bool $reget
     *
     * @return array
     */
    public function getModuleList(bool $reget = false): array
    {
        if (!$reget && $this->module_list) {
            return $this->module_list;
        }
        $this->module_list = (new Modules())->getList();

        return $this->module_list;
    }

    /**
     * @DESC          # 获取模块信息
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/17 9:13
     * 参数区：
     *
     * @param string $module_name
     *
     * @return mixed
     */
    public function getModuleInfo(string $module_name): mixed
    {
        if ($modules = $this->getModuleList()) {
            if (isset($modules[$module_name])) {
                return $modules[$module_name];
            }
        }
        return null;
    }

    static function getCommands(): array
    {
        $commands = [];
        if (file_exists(Env::path_COMMANDS_FILE)) $commands = (array)require self::path_COMMANDS_FILE;
        return $commands;
    }

}
