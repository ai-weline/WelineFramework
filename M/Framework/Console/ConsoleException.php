<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/10
 * 时间：23:47
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console;


class ConsoleException extends \M\Framework\App\Exception
{
    public $printer;

    function __construct($message, $code = 0)
    {
        $this->printer = new \M\Framework\Output\Cli\Printing();
        parent::__construct($message, $code);
        $this->message = $this->__toString();
    }

    public function __toString()
    {
        return $this->message = $this->printer->colorize($this->message, $this->printer::FAILURE) . PHP_EOL;
    }

}