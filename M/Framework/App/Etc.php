<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/21
 * 时间：14:06
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\App;


use M\Framework\App\Etc\Modules;

class Etc
{
    // 路径
    const path_ENV_FILE = APP_ETC_PATH . 'env.php';
    const path_MODULES_FILE = APP_ETC_PATH . 'modules.php';
    const path_COMMANDS_FILE = APP_ETC_PATH . 'generated/commands.php';
    const path_API_ROUTER_FILE = APP_ETC_PATH . 'generated/api_routers.php';
    const path_PC_ROUTER_FILE = APP_ETC_PATH . 'generated/pc_routers.php';
    const path_TRANSLATE_WORDS_FILE = APP_ETC_PATH . 'generated/language.php';

    // 日志
    const log_path_ERROR = 'error';
    const log_path_EXCEPTION = 'exception';
    const log_path_NOTICE = 'notice';
    const log_path_WARNING = 'warning';
    const log_path_DEBUG = 'debug';

    // 变量
    /**
     * @var Etc|null
     */
    private static ?Etc $instance;


    public array $config;
    const default_LOG = array(
        'error' => BP . 'var/log/error.log',
        'exception' => BP . 'var/log/exception.log',
        'notice' => BP . 'var/log/notice.log',
        'warning' => BP . 'var/log/warning.log',
        'debug' => BP . 'var/log/debug.log'
    );

    /**
     * @DESC         |私有化克隆函数
     *
     * 参数区：
     *
     */
    private function __clone()
    {
    }

    /**
     * @DESC         |获得实例
     *
     * 参数区：
     *
     * @return Etc
     */
    static public function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Etc 私有化 初始函数...
     */
    private function __construct()
    {
        $this->config = include APP_ETC_PATH . 'env.php';
        $this->config['log'] = isset($this->config['log']) ? $this->config['log'] : self::default_LOG;
    }

    /**
     * @DESC         |获取环境参数
     *
     * 参数区：
     *
     * @param string $name
     * @param array $default
     * @return array
     */
    function getConfig(string $name = '', $default = array())
    {
        if ('' == $name)
            return $this->config;
        return $this->config[$name] ?? $default;
    }

    /**
     * @DESC         |读取log路径
     *
     * 参数区：
     *
     * @param string $type
     * @return string
     */
    function getLogPath(string $type): string
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
    function getDbConfig(): array
    {
        return $this->config['db'];
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return array
     */
    function getModuleList()
    {
        return (new Modules())->getList();
    }
}