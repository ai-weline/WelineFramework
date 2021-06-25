<?php
/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Model;


use Weline\Framework\System\File\Scanner;

class Reader extends \Weline\Framework\System\ModuleFileReader
{
    function __construct(Scanner $scanner, $path = 'Model' . DIRECTORY_SEPARATOR)
    {
        parent::__construct($scanner, $path);
    }

    /**
     * @DESC |
     * 参数区：
     */
    function read(){
        $file_list = $this->getFileList();
        p($file_list);
    }
}