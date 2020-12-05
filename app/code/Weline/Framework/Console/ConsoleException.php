<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console;

class ConsoleException extends \Weline\Framework\App\Exception
{
    public $printer;

    public function __construct($message, $code = 0)
    {
        $this->printer = new \Weline\Framework\Output\Cli\Printing();
        parent::__construct($message, $code);
        $this->message = $this->__toString();
    }

    public function __toString()
    {
        return $this->message = $this->printer->colorize($this->message, $this->printer::FAILURE) . PHP_EOL;
    }
}
