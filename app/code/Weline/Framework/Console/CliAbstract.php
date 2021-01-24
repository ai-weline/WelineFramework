<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console;

use Weline\Framework\App\Env;
use Weline\Framework\System\File\Io\File;

abstract class CliAbstract implements CommandInterface
{
    const RETURN_FAILURE = '异常退出！';

    /**
     * @var array
     */
    protected $argv;

    /**
     * @var string
     */
    protected $msg;

    /**
     * @var string
     */
    protected $command_file;

    /**
     * @var \ReflectionClass
     */
    protected $reflection_class;

    public $printer;

    /**
     * Cli 初始函数...
     * @param string $msg
     * @param array $argv
     * @throws \ReflectionException
     */
    public function __construct($msg, $argv)
    {
        $this->command_file     = array_shift($argv);
        $this->argv             = $argv;
        $this->msg              = $msg;
        $this->reflection_class = new \ReflectionClass($this);
        $this->printer          = new \Weline\Framework\Output\Cli\Printing();
    }

    /**
     * @DESC         |命令提示
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @return string
     */
    public function getTip(): string
    {
        return 'CLI ' . self::version;
    }

    /**
     * @DESC         |方法描述
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param $command
     * @return string
     */
    protected function getCommandPath($command = null): string
    {
        $command_array = explode(':', $command);
        foreach ($command_array as &$command) {
            $command = ucfirst($command);
        }

        return '\\' . self::dir . '\\' . implode('\\', $command_array);
    }

    /**
     * @DESC         |读取默认命令提示
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string|null $group
     * @throws \Weline\Framework\App\Exception
     * @return mixed|void
     */
    public function execute($group = null)
    {
        $commands = isset($this->getCommandList()[$group]) ? $this->getCommandList()[$group] : $this->getCommandList();

        $this->printer->printList($commands);
    }

    /**
     * @DESC         |获取命令列表
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @throws \Weline\Framework\App\Exception
     * @return array|string[][]
     */
    public function getCommandList(): array
    {
        // 扫描所有命令
        $file_path = Env::path_COMMANDS_FILE;
        if (is_file($file_path)) {
            $commands = include $file_path;
            if (empty($commands)) {
                exec('php ' . BP . 'bin/m command:upgrade', $result);
                return  include $file_path;
            }

            return $commands;
        }

        $file = new File();
        $file->open($file_path);
        $text = '<?php return array();';
        $file->write($text);
        $file->close();
        exec('php ' . BP . 'bin/m module:command:upgrade');

        return require $file_path;
    }
}
