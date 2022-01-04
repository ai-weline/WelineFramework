<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console;

use Weline\Framework\App;
use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Manager\ObjectManager;

class Cli extends CliAbstract
{
    const core_FRAMEWORK_NAMESPACE = Env::framework_name . '\\Framework';

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @throws ConsoleException
     * @throws Exception
     */
    public function run()
    {
        // 没有任何参数
        if (! isset($this->argv[0])) {
            exit($this->execute());
        }
        $command_class = $this->checkCommand();
//        $this->printer->note(__('执行命令：') . $class['command'] . ' ' . (isset($this->argv[1])?$this->argv[1]:''));
        switch (count($this->argv)) {
            case 1:
                ObjectManager::getInstance($command_class['class'])->execute();
                break;
            default:
                ObjectManager::getInstance($command_class['class'])->execute($this->argv);
                break;
        }
        $this->printer->printing("\n");
        $this->printer->note(__('执行命令：') . $command_class['command'] . ' ' . ($this->argv[1] ?? '')/*,$this->printer->colorize('CLI-System','red')*/);
    }

    /**
     * @DESC         |简化推荐命令
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array $commands
     * @return array
     */
    private function recommendCommand(array $commands): array
    {
        // 新算法

        // 旧算法
        $arg0              = strtolower(trim($this->argv[0]));
        $input_command_arr = explode(':', $arg0);
        $recommendCommands = [];
        $matchCommand      = [];
        foreach ($commands as $group => $command) {
            $keys = array_keys($command);
            foreach ($keys as $command_key) {
                // 匹配参考
                if (strstr($command_key, $arg0)) {
                    $matchCommand[$group][] = [$command_key => $command[$command_key]];
                }
                $command_key_arr = explode(':', $command_key);
                $k               = 0;
                foreach ($input_command_arr as $input_key => $input_command_head) {
                    // 如果长度和首匹配都相同
                    if (count($command_key_arr) === count($input_command_arr)) {
                        if ($input_command_head) {
                            $input_str_pos = strpos($command_key_arr[$input_key], $input_command_head);
                            if (isset($command_key_arr[$input_key]) && ! is_bool($input_str_pos) && $input_str_pos === 0) {
                                $k += 1;
                            }
                        }
                    }
                }
                if (count($input_command_arr) === $k) {
                    $matchCommand[$group][] = [$command_key => $command[$command_key]];
                }
                if ($k > 0) {
                    $recommendCommands[$group][] = [$command_key => $command[$command_key]];
                }
            }
        }

        return $matchCommand ?? $recommendCommands;
    }

    /**
     * @DESC         |检查命令
     *
     * 参数区：
     *
     * @throws ConsoleException
     * @throws Exception
     * @return array
     */
    private function checkCommand(): array
    {
        $arg0 = strtolower(trim($this->argv[0]));
        if ($arg0 === 'command:upgrade') {
            try {
                ObjectManager::getInstance(\Weline\Framework\Console\Command\Upgrade::class)->execute();
            } catch (Exception $exception) {
                $this->printer->error($exception->getMessage());
                exit();
            }
        }
        $commands = Env::getCommands();
        if ($arg0 !== 'command:upgrade' && empty($commands)) {
            exit($this->printer->error('命令系统异常！请完整执行（不能简写）更新模块命令后重试：php bin/m command:upgrade'));
        }

        // 检查命令
        $command_path = '';
        foreach ($commands as $group => $group_commands) {
            if (array_key_exists($arg0, $group_commands)) {
                $group_arr    = explode('#', $group);
                $command_path = array_pop($group_arr);
            }
        }

        if ($command_path) {
            // 获取类的真实路径和命名空间位置
            $command_class_path = $command_path . $this->getCommandPath($arg0);
            $command_real_path  = APP_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $command_class_path) . '.php';
            if (file_exists($command_real_path)) {
                return ['class' => $command_class_path, 'command' => $arg0];
            }
        }

        $recommendCommands = $this->recommendCommand($commands);
        $commands          = [];
        foreach ($recommendCommands as $recommendCommand) {
            $commands = array_merge($commands, $recommendCommand);
        }
        if (count($commands) === 1) {
            $command            = array_shift($commands);
            $command_keys       = array_keys($command);
            $command            = array_shift($command_keys);
            $group_keys         = array_keys($recommendCommands);
            $group              = array_shift($group_keys);
            $group_arr          = explode('#', $group);
            $command_path       = array_pop($group_arr);
            $command_class_path = $command_path . $this->getCommandPath($command);

            $command_real_path = APP_PATH . str_replace('\\', DIRECTORY_SEPARATOR, $command_class_path) . '.php';
            if (file_exists($command_real_path)) {
                return ['class' => $command_class_path, 'command' => $command];
            }
            if (DEV) {
                throw new ConsoleException('命令文件缺失：' . $command_real_path);
            }
        }
        foreach ($recommendCommands as $key => &$command) {
            foreach ($command as $k => $item) {
                unset($command[$k]);
                $keys                        = array_keys($item);
                $command[array_shift($keys)] = array_pop($item);
            }
        }
        $this->printer->error('无效命令：' . $arg0, 'CLI');
        $this->printer->note('参考命令', '系统');
        $this->printer->printList($recommendCommands);
        exit();
    }
}
