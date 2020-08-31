<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/24
 * 时间：10:34
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Setup\UnitTest;
require __DIR__ . '/../../../../index.php';

use M\Framework\Setup\Db\Setup;
use PHPUnit\Framework\TestCase;

class DbSetupTest extends TestCase
{
    private Setup $setup;


    protected function setUp(): void

    {

        $this->setup = new Setup();

    }


    protected function tearDown(): void

    {

        unset($this->setup);

    }


    public function testIsExistTable()

    {
        $result = $this->setup->tableExist('m_aiweline_news');
        p($result);
//        $this->assertEquals(4, $result);
    }
}
