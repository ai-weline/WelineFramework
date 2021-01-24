<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/1/20
 * 时间：23:24
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\System;


use Weline\Framework\DataObject\DataObject;
use Weline\Framework\System\File\Scanner;

class FileReader extends DataObject
{
    /**
     * @var Scanner
     */
    private Scanner $scanner;

    private array $fileList = [];
    private string $path;

    function __construct(
        Scanner $scanner,
        $path = 'etc' . DIRECTORY_SEPARATOR . 'module.xml'
    )
    {
        $this->scanner = $scanner;
        $this->path = $path;
        $this->fileList = $this->scanner->scanVendorModulesWithFiles($path);
    }

    function getFileList()
    {
        return $this->fileList;
    }

    function getFilePath()
    {
        return $this->path;
    }
}