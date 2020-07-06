<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/15
 * 时间：22:33
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\FileSystem;


use M\Framework\App\Exception;
use M\Framework\FileSystem\Data\File;

class Scan
{

    private array $dirs = array();

    private int $keepLevel = 0;

    /**
     * @DESC         |方法描述
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $dirPath
     * @param int $level
     * @return array
     */
    function scanDirTree(string $dirPath, int $level = 0): array
    {
        $this->keepLevel += 1;
        if ($file_handler = opendir($dirPath)) {
            while (false !== ($file = readdir($file_handler))) {
                // 排除"."".."
                if ($file != "." && $file != "..") {
                    $filename = $dirPath . DIRECTORY_SEPARATOR . $file;
                    $relateFilename = str_replace(APP_PATH, '', $filename);
                    if (is_dir($filename)) {
                        // 目录层级：是否扫描
                        if ($level) {
                            if ($this->keepLevel < $level) {
                                $this->scanDirTree($filename, $level);//递归调用;
                            }
                        } else {
                            // 扫描全部目录
                            $this->scanDirTree($filename);
                        }
                    } else {
                        // 文件
                        $file = new File();
                        $pathInfo = pathinfo($filename);
                        $file->setBasename($pathInfo['basename']);
                        $file->setFilename($pathInfo['filename']);
                        $file->setDirname($pathInfo['dirname']);
                        $file->setExtension($pathInfo['extension']);
                        $file->setOrigin($filename);
                        $file->setNamespace(str_replace('/', '\\', dirname($relateFilename)));
                        $file->setRelate($relateFilename);
                        $file->setSize(filesize($filename));
                        $file->setType(filetype($filename));
                        $this->dirs[dirname($relateFilename)][] = $file;
                    }
                }
            }
        }
        return $this->dirs;
    }

    /**
     * @DESC         |扫描目录
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $dirPath
     * @return array
     */
    function scanDir(string $dirPath):array
    {
        if(!is_dir($dirPath)) return [];
        if ($this->dirs = (scandir($dirPath)) ? scandir($dirPath) : []) {
            // 排除"."".."
            array_shift($this->dirs);
            array_shift($this->dirs);
        }
        return $this->dirs;
    }

    function dirToArray($dir)
    {
        $contents = array();
        # Foreach node in $dir
        foreach (scandir($dir) as $node) {
            # Skip link to current and parent folder
            if ($node == '.') continue;
            if ($node == '..') continue;
            # Check if it's a node or a folder
            if (is_dir($dir . DIRECTORY_SEPARATOR . $node)) {
                # Add directory recursively, be sure to pass a valid path
                # to the function, not just the folder's name
                $contents[$node] = $this->dirToArray($dir . DIRECTORY_SEPARATOR . $node);
            } else {
                # Add node, the keys will be updated automatically
                $contents[] = $node;
            }
        }
        # done
        return $contents;
    }
}