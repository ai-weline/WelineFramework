<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Output\Debug;

use Weline\Framework\App\Env;

class Printing extends AbstractPrint
{
    private ?Env $etc;

    public function __construct()
    {
        $this->etc = Env::getInstance();
    }

    /**
     * @DESC         |日志记录
     *
     * 参数区：
     *
     * @param $message
     * @param int $message_type
     * @param string $log_path
     */
    public function debug($message, string $log_path = null, int $message_type = 3)
    {
        if ($log_path === null) {
            $log_path = $this->etc->getLogPath(Env::log_path_ERROR);
        }
        $this->write($log_path, is_array($message) ? var_export($message, true) : $message, $message_type);
    }
}
