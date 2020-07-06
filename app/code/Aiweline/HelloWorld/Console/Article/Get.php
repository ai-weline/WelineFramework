<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/21
 * 时间：15:19
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\HelloWorld\Console\Article;


use M\Framework\Console\CommandInterface;

class Get implements CommandInterface
{

    public function execute($args=array())
    {
        exit(' // TODO: Implement execute() method.');
    }

    public function getTip(): string
    {
        return 'getTip(111111111)';
    }
}