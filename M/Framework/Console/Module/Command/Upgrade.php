<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/21
 * 时间：13:22
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\Module\Command;


use M\Framework\App;
use M\Framework\App\Etc;
use M\Framework\Console\Command;
use M\Framework\Console\CommandAbstract;
use M\Framework\FileSystem\Data\File;
use M\Framework\FileSystem\Scan;

class Upgrade extends CommandAbstract
{
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
     * @return mixed|void
     * @throws \M\Framework\App\Exception
     */
    public function execute($args = array())
    {
        // 删除命令文件
        if (is_file(ETC::path_COMMANDS_FILE)) exec(App::helper()->getConversionCommand('rm', ' ') . ETC::path_COMMANDS_FILE);

        $commands = $this->scan();

        $file = new \M\Framework\FileSystem\Io\File();
        $file->open(Etc::path_COMMANDS_FILE, $file::mode_w_add);
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
    function scan()
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
        $command_class_position = new Command();
        $commands = [];
        $scanner = new Scan();

        // 扫描核心命令
        $directory = DIRECTORY_SEPARATOR;
        $core = $scanner->scanDirTree(FP . "Framework{$directory}Console");
        $customer = $scanner->scanDirTree(APP_PATH);

        // 合并
        $command_dir_files = array_merge($core, $customer);
        /** @var $command_files File[] */
        foreach ($command_dir_files as $dir => $command_files) {
            if (is_string($dir) && strstr($dir, self::dir)) {
                if ('WINNT' === PHP_OS) $dir = str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $dir);
                $dir_command_array = explode(self::dir, $dir);
                $command_dir = trim(array_pop($dir_command_array), DIRECTORY_SEPARATOR);
                $module_dir_arr = explode(DIRECTORY_SEPARATOR, trim(array_pop($dir_command_array), DIRECTORY_SEPARATOR));
                $module = array_pop($module_dir_arr);
                $vendor = array_pop($module_dir_arr);
                $module_name = $vendor . '\\' . $module;
                if ($command_dir) {
                    foreach ($command_files as $file) {
                        $command_dir_file = $file->getNamespace() . '\\' . $file->getFilename();
                        $command_dir_file_arr = explode(self::dir, $command_dir_file);
                        $command_dir_file = trim(array_pop($command_dir_file_arr), DIRECTORY_SEPARATOR);
                        $command = str_replace('\\', ':', strtolower($command_dir_file));
                        $command = trim($command, ':');
                        if ($command) {
                            $command_class_path = $command_class_position->getCommandPath($module_name, $command);
                            $command_class = new $command_class_path();
                            $commands[str_replace(DIRECTORY_SEPARATOR, ':', strtolower($command_dir)) . '#' . $module_name][$command] = $command_class->getTip();
                        }
                    }
                }
            }
        }
        return $commands;
    }

}