<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\Config;

use Weline\Framework\Config\Xml\Reader;
use Weline\Framework\System\File\Scanner;
use Weline\Framework\Xml\Parser;

class MenuReader extends Reader
{
    function __construct(Scanner $scanner, Parser $parser, $path = 'adminhtml/menu.xml')
    {
        parent::__construct($scanner, $parser, $path);
    }
}