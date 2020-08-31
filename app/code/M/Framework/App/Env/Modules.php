<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/3
 * 时间：15:46
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\App\Env;


use M\Framework\FileSystem\Io\File;

class Modules
{
    /**
     * @DESC         |获取已经安装的模块列表
     *
     * 参数区：
     *
     * @return array
     */
    function getList()
    {
        $modules_file = APP_ETC_PATH . 'modules.php';
        if (!is_file($modules_file)) {
            $file = new File();
            $file->open($modules_file, $file::mode_w_add);
            $text = '<?php return ' . var_export([], true) . ';?>';
            $file->write($text);
            $file->close();
        }
        $modules_data = include $modules_file;
        return is_array($modules_data) ? $modules_data : array();
    }
}