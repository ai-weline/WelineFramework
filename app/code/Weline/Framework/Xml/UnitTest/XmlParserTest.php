<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Xml\UnitTest;

use Weline\Framework\Xml\Parser;
use PHPUnit\Framework\TestCase;
use Weline\Framework\UnitTest\Boot;

class XmlParserTest extends TestCase
{
    use Boot;

    protected Parser $parser;

    public function setUp(): void
    {
        $this->parser = new Parser();
    }

    public function testParser()
    {
        p($this->parser->load(__DIR__ . '/menu.xml'));
    }
}
