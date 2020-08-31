<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/4
 * 时间：22:56
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Env\test;

use M\Framework\Env\CheckEnv;
use PHPUnit\Framework\TestCase;
use M\Framework\Manager\ObjectManager;
require __DIR__ . '/../../../../setup/setup.php';
class CheckEnvTest extends TestCase
{
    function testSetNeed()
    {
        $envCheck = ObjectManager::getInstance(CheckEnv::class);
        $result = $envCheck->setNeed('module1', 'json');
        p($result);
    }
}
