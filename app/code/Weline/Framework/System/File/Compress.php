<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\System\File;

use Phar;
use PharData;
use Weline\Framework\App\Exception;

class Compress
{
    private array $files = [];

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param $path
     * @param string $name
     * @return PharData
     */
    public function createTarGz($path, $name = '')
    {
        $parent    = dirname($path);
        $file_name = $name ? $name : $parent;
        $file_name = $file_name . '.tar';

        try {
            $tar = new PharData($file_name);
            // 添加 archive.tar 归档文件
            if ($this->files) {
                foreach ($this->files as $file) {
                    $tar->addFile($file);
                }
            }

            // 压缩 archive.tar 文件. 压缩完成后的文件为 archive.tar.gz
            $tar->buildFromDirectory($parent, '#^' . preg_quote("$parent/parent-folder/", '#') . '#');
            $tar->compress(Phar::GZ);
            // 请注意，这两个文件都将存在。所以如果你想的话可以取消链接 archive.tar
//            unlink($file_name);
            if ($name) {
                $tar->setAlias($name);
            }

            return $tar;
        } catch (Exception $e) {
            echo 'Exception : ' . $e;
            exit();
        }
    }

    /**
     * @DESC         |添加文件
     *
     * 参数区：
     *
     * @param $file
     * @throws Exception
     */
    public function addFiles($file)
    {
        if (is_array($file)) {
            $this->files = array_unique(array_merge($this->files, $file));
        } else {
            if (is_file($file)) {
                $this->files[] = $file;
            } else {
                throw new Exception(__('不存在的文件！'));
            }
        }
    }
}
