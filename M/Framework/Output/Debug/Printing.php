<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/15
 * 时间：23:40
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Output\Debug;


use M\Framework\App\Etc;
use M\Framework\FileSystem\Io\File;

class Printing extends AbstractPrint
{
    private $etc;

    function __construct()
    {
        $this->etc = Etc::getInstance();
    }

    /**
     * @DESC         |日志记录
     *
     * 参数区：
     *
     * @param string $message
     * @param int $message_type
     * @param string $log_path
     */
    function debug(string $message, string $log_path = null, int $message_type = 3)
    {
        if ($log_path == null) $log_path = $this->etc->getLogPath(Etc::log_path_ERROR);
        $this->write($log_path, $message, $message_type);
    }
}