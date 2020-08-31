<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/26
 * 时间：12:39
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Manager\UnitTest;

use Aiweline\Admin\Controller\Index;
use M\Framework\Manager\ObjectManager;
use M\Framework\UnitTest\Core;
use PHPUnit\Framework\TestCase;

class ObjectManagerTest extends TestCase
{
    use Core;
    private ObjectManager $instance ;
    function setUp(): void
    {
       $this->instance = ObjectManager::getInstance();
    }

    function testGetInstance(){
        /**
         * @var $index Index
         */
        $index = $this->instance->getInstance(Index::class);
        p($index->test()); 
    }
}
