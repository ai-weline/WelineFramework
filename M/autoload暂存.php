<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/9
 * 时间：23:00
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */
// 类自动加载
spl_autoload_register(
/**
 * @param $name
 * @return bool
 */
    function ($name) {
        $file_name = substr($name, strrpos($name, '\\'), strlen($name));
        $file_path = substr($name, strpos($name, '\\'));
        // 处理特殊路径
//        $spacename_dir = substr($name, 0, strrpos($name, '\\'));
        $spacename_dir = substr($file_path, 0, strrpos($name, '\\'));
        $dir_path = FP . str_replace('\\', '/', $spacename_dir);
        $file_path = $dir_path . str_replace('\\', '/', $file_name) . '.php';
        if (file_exists($file_path)) {
            try {
                require $file_path;
                return true;
            } catch (\Exception $e) {
                // 应用vendor文件
                require BP . '../vendor/autoload.php';
                require $file_path;
            } finally {
                throw new Exception("Unable to load $name. Path:$file_path" . $e->getMessage());
            }
        } else {
            throw new Exception("Unable to load $name File not exsit! Path:$file_path");
        }
    }
);