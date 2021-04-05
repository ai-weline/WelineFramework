<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\App;

use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\System\Path\Directory;

class System
{
    /**
     * @DESC         |执行linux命令兼容win
     *
     * 参数区：
     *
     * @param string $linux_command
     * @param bool $preview
     * @return array
     */
    public function exec(string $linux_command, bool $preview = false)
    {
        if (IS_WIN) {
            // 删除
            if (
                strstr($linux_command, 'rm') &&
                strstr($linux_command, '-rf')
            ) {
                $linux_command = str_replace('rm', 'rd', $linux_command);
                $linux_command = str_replace('-rf', '-r', $linux_command);
            }
            $linux_to_win = [
                'rm' => 'del',
                '-f' => '/F',
                'cp' => 'xcopy',
                '-r' => '/S/Q',
            ];

            foreach ($linux_to_win as $key => $item) {
                $linux_command = str_replace($key, $item, $linux_command);
            }
        }
        if ($preview) {
            return $linux_command;
        }
        exec($linux_command, $output, $return_var);

        return [$output, $return_var];
    }

    public function input()
    {
        // 判断系统
        if (IS_WIN) {
            $input = fread(STDIN, 1024);
        } else {
            $fp    = fopen('/dev/stdin', 'r');
            $input = fgets($fp, 1024);
            fclose($fp);
        }

        return $input;
    }

    /**
     * @DESC         |获得目录对象
     *
     * 参数区：
     *
     * @throws \ReflectionException
     * @return Directory
     */
    public function getDirectoryObject(): Directory
    {
        return ObjectManager::getInstance(Directory::class);
    }
}
