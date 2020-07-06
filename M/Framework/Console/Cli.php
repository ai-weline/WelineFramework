<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/10
 * 时间：22:03
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console;


use M\Framework\App\Etc;
use M\Framework\App\Exception;

class Cli extends CliAbstract
{
    const core_FRAMEWORK_NAMESPACE = 'M\\Framework';

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @throws ConsoleException
     * @throws Exception
     */
    function run()
    {
        $class = $this->checkCommand();
        switch (count($this->argv)) {
            case 1:
                echo $class->execute();
                break;
            default:
                echo $class->execute($this->argv);
                break;
//                try {
//                    echo $this->execute();
//                } catch (\Exception $e) {
//                    $this->printer->error($e->getMessage(), $this->printer::ERROR);
//                }
        }
    }

    /**
     * @DESC         |推荐命令
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array $commands
     */
    private function recommendCommand(array $commands)
    {
        $arg0 = trim($this->argv[0]);
        $command_group_arr = explode(':', $arg0);
        $command_group_arr = array_reverse($command_group_arr);
        foreach ($command_group_arr as $command_group) {
            foreach ($commands as $group => $command) {
                if (strstr($command_group, $group)) {
                    $this->printer->note('参考命令:');
                    try {
                        echo $this->printer->printList($commands[$command_group]);
                    } catch (Exception $e) {
                        $this->printer->error($arg0, '无法执行的命令:' . $e->getMessage(), $this->printer::WARNING);
                    }
                }
            }

        }
    }

    /**
     * @DESC         |检查命令
     *
     * 参数区：
     *
     * @return CommandInterface
     * @throws ConsoleException
     * @throws Exception
     */
    private function checkCommand()
    {
        $arg0 = trim($this->argv[0]);
        if ($arg0 == 'module:command:upgrade') exit((new \M\Framework\Console\Module\Command\Upgrade())->execute());
        if ($arg0 != 'module:command:upgrade' && !file_exists(Etc::path_COMMANDS_FILE)) exit($this->printer->error('请更新模块命令：module:command:upgrade'));

        $commands = include Etc::path_COMMANDS_FILE;
        // 检查命令
        $command_path = '';
        foreach ($commands as $group => $group_commands) {
            if (array_key_exists($arg0, $group_commands)) {
                $group_arr = explode('#', $group);
                $command_path = array_pop($group_arr);
            };
        }
        if (empty($command_path)) {
            $this->recommendCommand($commands);
            $this->printer->error('无效命令：' . $arg0);
        }

        // 获取类的真实路径和命名空间位置
        if ($command_path !== self::core_FRAMEWORK_NAMESPACE) {
            $command_class_real_path = APP_PATH . $command_path;
        } else {
            $command_class_real_path = BP . $command_path;
        }
        $command_real_path = str_replace('\\', '/', $command_class_real_path) . str_replace('\\', '/', $this->getCommandPath($arg0)) . '.php';
        $command_class_path = $command_path . $this->getCommandPath($arg0);
        if (file_exists($command_real_path)) {
            return new $command_class_path();
        } else {
            throw new ConsoleException('命令文件缺失：' . $command_real_path);
        }
    }
}