<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/20
 * 时间：16:22
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\FileSystem\Io;


use M\Framework\App\Exception;
use M\Framework\Console\ConsoleException;

class File
{
    const mode_r = 'r';//只读。在文件的开头开始。
    const mode_r_add = 'r+';//只读。在文件的开头开始。
    const mode_w = 'w';//只写。打开并清空文件的内容；如果文件不存在，则创建新文件。
    const mode_w_add = 'w+';//读/写。打开并清空文件的内容；如果文件不存在，则创建新文件。
    const mode_a = 'a';//追加。打开并向文件文件的末端进行写操作，如果文件不存在，则创建新文件。
    const mode_a_add = 'a+';//读/追加。通过向文件末端写内容，来保持文件内容。
    const mode_x = 'x';//只写。创建新文件。如果文件以存在，则返回 FALSE。
    const mode_x_add = 'x+';//读/写。创建新文件。如果文件已存在，则返回 FALSE 和一个错误。
    /**
     * @var false|resource
     */
    protected $_file;

    /**
     * @var string
     */
    protected $_filename;

    /**
     * @DESC         |打开文件流
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     * @param string $filename
     * @param string $mode
     * @return File
     */
    function open(string $filename, string $mode = self::mode_a_add)
    {
        $position = strrpos($filename, DIRECTORY_SEPARATOR);
        $path = substr($filename, 0, $position);
        if (!file_exists($path)) {
            mkdir($path, 0770, true);
        }
        $this->_filename = $filename;
        $this->_file = fopen($filename, $mode);
        return $this;
    }

    /**
     * @DESC         |写入文件流
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $content
     * @return File
     * @throws Exception
     */
    function write(string $content)
    {
        if (!$this->_file) {
            if (PHP_SAPI != 'cli') throw new Exception("文件:{$this->_filename} 读取异常！");
            throw new ConsoleException("文件:{$this->_filename} 读取异常！");
        }
        fwrite($this->_file, $content);
        return $this;
    }

    /**
     * @DESC         |关闭文件
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @return bool
     */
    function close()
    {
        return fclose($this->_file);
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return false|resource
     * @throws Exception
     */
    function getSource()
    {
        if (!$this->_file) {
            throw new Exception("文件:{$this->_filename} 读取异常！");
        }
        return $this->_file;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $filename
     * @return bool|string
     */
    function create(string $filename)
    {
        if (file_exists($filename)) return $filename;
        $dir = dirname($filename);
        if (is_dir($dir)) @mkdir($dir, 0770);
        $this->open($filename, self::mode_w);
        $this->close();
        if (file_exists($filename)) return $filename;
        return false;
    }

    /**
     * @DESC         |处理文件是否存在
     *
     * 参数区：
     * @param string $filename
     * @return bool|string
     */
    function processFile(string $filename)
    {
        if (file_exists($filename)) return $filename;
        return $this->create($filename);
    }
}