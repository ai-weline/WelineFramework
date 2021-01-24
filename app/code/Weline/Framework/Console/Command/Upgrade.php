<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console\Command;

use Weline\Framework\App\System;
use Weline\Framework\App\Env;
use Weline\Framework\Console\Command;
use Weline\Framework\Console\CommandAbstract;
use Weline\Framework\System\File\Data\File;
use Weline\Framework\System\File\Scan;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Cli\Printing;

class Upgrade extends CommandAbstract
{
    /**
     * @var System
     */
    private System $system;

    public function __construct(
        Printing $printer,
        System $system
    ) {
        parent::__construct($printer);
        $this->printer = $printer;
        $this->system  = $system;
    }

    /**
     * @DESC         |命令描述
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
        return '更新命令';
    }

    /**
     * @DESC         |执行
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array $args
     * @throws \Weline\Framework\App\Exception
     * @return mixed|void
     */
    public function execute($args = [])
    {
        // 删除命令文件
        if (is_file(Env::path_COMMANDS_FILE)) {
            list($out, $var) = $this->system->exec('rm ' . Env::path_COMMANDS_FILE);
            $this->printer->printList($out);
        }

        $commands = $this->scan();
        /**@var $file \Weline\Framework\System\File\Io\File */
        $file = ObjectManager::getInstance('\Weline\Framework\System\File\Io\File');
        $file->open(Env::path_COMMANDS_FILE, $file::mode_w_add);
        $text = '<?php return ' . var_export($commands, true) . ';';
        $file->write($text);
        $file->close();

        $this->printer->printList($commands);
    }

    /**
     * @DESC         |扫描命令
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @return array
     */
    public function scan()
    {
        return $this->getDirFileCommand();
    }

    /**
     * @DESC         |文件转换命令
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @return array
     */
    private function getDirFileCommand()
    {
        $command_class_position = ObjectManager::getInstance(Command::class);
        $commands               = [];
        /**@var $scanner Scan */
        $scanner = ObjectManager::getInstance(Scan::class);

        // 扫描核心命令
        $core     = $scanner->scanDirTree(Env::vendor_path);
        $customer = $scanner->scanDirTree(APP_PATH);

        // 合并
        $command_dir_files = array_merge($core, $customer);
        /** @var $command_files File[] */
        foreach ($command_dir_files as $dir => $command_files) {
            if (is_string($dir) && strstr($dir, self::dir)) {
                if (IS_WIN) {
                    $dir = str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $dir);
                }
                $dir_command_array = explode(self::dir, $dir);
                $command_dir       = trim(array_pop($dir_command_array), DIRECTORY_SEPARATOR);
                $module_dir_arr    = explode(DIRECTORY_SEPARATOR, trim(array_pop($dir_command_array), DIRECTORY_SEPARATOR));
                $module            = array_pop($module_dir_arr);
                $vendor            = array_pop($module_dir_arr);
                $module_name       = $vendor . '\\' . $module;
                if ($command_dir) {
                    foreach ($command_files as $file) {
                        $command_dir_file     = $file->getNamespace() . '\\' . $file->getFilename();
                        $command_dir_file_arr = explode(self::dir, $command_dir_file);
                        $command_dir_file     = trim(array_pop($command_dir_file_arr), DIRECTORY_SEPARATOR);
                        $command              = str_replace('\\', ':', strtolower($command_dir_file));
                        $command              = trim($command, ':');
                        if ($command) {
                            $command_class_path                                                                                       = $command_class_position->getCommandPath($module_name, $command);
                            $command_class                                                                                            = ObjectManager::getInstance($command_class_path);
                            $commands[str_replace(DIRECTORY_SEPARATOR, ':', strtolower($command_dir)) . '#' . $module_name][$command] = $command_class->getTip();
                        }
                    }
                }
            }
        }

        return $commands;
    }
}
