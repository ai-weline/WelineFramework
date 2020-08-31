<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/15
 * 时间：22:10
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console;


use M\Framework\App\Env;
use M\Framework\FileSystem\Io\File;

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
    function __construct($msg, $argv)
    {
        $this->command_file = array_shift($argv);
        $this->argv = $argv;
        $this->msg = $msg;
        $this->reflection_class = new \ReflectionClass($this);
        $this->printer = new \M\Framework\Output\Cli\Printing();
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
     * @return mixed|void
     * @throws \M\Framework\App\Exception
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
     * @return array|string[][]
     * @throws \M\Framework\App\Exception
     */
    public function getCommandList(): array
    {
        // 扫描所有命令

        $file_path = Env::path_COMMANDS_FILE;
        if (is_file($file_path)) return include $file_path;

        $file = new File();
        $file->open($file_path);
        $text = '<?php return array();';
        $file->write($text);
        $file->close();
        return require $file_path;
    }

}