<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\System;

use Weline\Framework\DataObject\DataObject;
use Weline\Framework\System\File\Scanner;

class ModuleFileReader extends DataObject
{
    /**
     * @var Scanner
     */
    private Scanner $scanner;

    private array $fileList = [];

    private string $path;

    /**
     * ModuleFileReader 初始函数...
     * @param Scanner $scanner
     * @param string $path
     */
    public function __construct(
        Scanner $scanner,
        $path = 'etc' . DIRECTORY_SEPARATOR . 'module.xml'
    ) {
        $this->scanner  = $scanner;
        $this->path     = $path;
        $this->fileList = $this->scanner->scanVendorModulesWithFiles($path);
        parent::__construct();
    }

    public function getFileList()
    {
        return $this->fileList;
    }

    public function getFilePath()
    {
        return $this->path;
    }
}
