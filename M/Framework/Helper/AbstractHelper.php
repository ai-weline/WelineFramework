<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/26
 * 时间：11:41
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Helper;


use M\Framework\App\Env;
use M\Framework\Output\Debug\Printing;

class AbstractHelper
{
    protected Printing $_debug;

    function __construct()
    {
        $this->_debug = new Printing();
    }

    /**
     * @DESC         |默认填写linux命令，此函数根据linux命令行获取对应运行系统的命令获取命令转化
     *
     * 参数区：
     * @param string $linux_command
     * @param string $param_str
     * @return string
     */
    function getConversionCommand(string $linux_command, string $param_str = ' '): string
    {
        if ('Linux' === PHP_OS) return $linux_command . $param_str;
        $common_commands = array(
            'WINNT' => [
                'rm' => 'del'
            ]
        );
        return isset($common_commands[PHP_OS][$linux_command]) ? $common_commands[PHP_OS][$linux_command] . $param_str : $linux_command . $param_str;
    }
}