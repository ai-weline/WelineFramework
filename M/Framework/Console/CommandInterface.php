<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/10
 * 时间：23:16
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console;


/**
 * @Author       秋枫雁飞
 * @Email        aiweline@qq.com
 * @Forum        https://bbs.aiweline.com
 * @DESC         此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 *
 * Interface CommandInterface
 * @package M\Framework\Console
 */
interface CommandInterface
{
    const version = '1.0.0';
    const dir = 'Console';

    /**
     * @DESC         |命令行类接口运行方法
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array $args
     * @return mixed
     */
    public function execute($args=array());

    /**
     * @DESC         |命令注释
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @return string
     */
    public function getTip(): string;
}