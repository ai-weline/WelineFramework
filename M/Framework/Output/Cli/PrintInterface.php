<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/15
 * 时间：22:54
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Output\Cli;


interface PrintInterface extends \M\Framework\Output\PrintInterface
{
    /**
     * @DESC         |CLI 错误打印
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $data
     * @param string $message
     * @param string $color
     * @param int $pad_length
     * @return mixed
     */
    public function error($data = 'CLI Error!', string $message = '', string $color = self::ERROR, int $pad_length = 25);

    /**
     * @DESC         |CLI 成功打印
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $data
     * @param string $message
     * @param string $color
     * @param int $pad_length
     * @return mixed
     */
    public function success($data = 'CLI Success!', string $message = '', string $color = self::ERROR, int $pad_length = 25);

    /**
     * @DESC         |CLI 警告打印
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $data
     * @param string $message
     * @param string $color
     * @param int $pad_length
     * @return mixed
     */
    public function warning($data = 'CLI Warning!', string $message = '', string $color = self::ERROR, int $pad_length = 25);

    /**
     * @DESC         |CLI 提示打印
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $data
     * @param string $message
     * @param string $color
     * @param int $pad_length
     * @return mixed
     */
    public function note($data = 'CLI Note!', string $message = '', string $color = self::ERROR, int $pad_length = 25);
}