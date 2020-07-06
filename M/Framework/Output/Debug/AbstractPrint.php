<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/15
 * 时间：22:53
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Output\Debug;


abstract class AbstractPrint extends \M\Framework\Output\AbstractPrint implements PrintInterface
{
    public $out;

    /**
     * @DESC         |错误
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array|string $data
     * @param string $message
     * @param string $color
     * @param int $pad_length
     * @return mixed|void
     */
    public function error($data = 'Error!', string $message = '', string $color = self::ERROR, int $pad_length = 25)
    {
        $this->doPrint($data, $message, self::ERROR);
    }

    /**
     * @DESC         |成功
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array|string $data
     * @param string $message
     * @param string $color
     * @param int $pad_length
     * @return mixed|void
     */
    public function success($data = 'Success!', string $message = '', string $color = self::ERROR, int $pad_length = 25)
    {
        $this->doPrint($data, $message, self::SUCCESS);
    }

    /**
     * @DESC         |警告
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array|string $data
     * @param string $message
     * @param string $color
     * @param int $pad_length
     * @return mixed|void
     */
    public function warning($data = 'Warning!', string $message = '', string $color = self::WARNING, int $pad_length = 25)
    {
        $this->doPrint($data, $message, self::WARNING);
    }

    /**
     * @DESC         |提示
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array|string $data
     * @param string $message
     * @param string $color
     * @param int $pad_length
     * @return mixed|void
     */
    public function note($data = 'Note!', string $message = '', string $color = self::NOTE, int $pad_length = 25)
    {
        $this->doPrint($data, $message, self::NOTE);
    }


    /**
     * ----------------辅助方法-------------------
     */
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
     * @param string|array $data
     * @param string $message
     * @param string $color
     * @param int $pad_length
     */
    public function doPrint($data, $message, $color, $pad_length = 0)
    {
        if (is_array($data)) {
            foreach ($data as $msg) {
                $this->printing($msg, $message, $color, $pad_length);
            }
        }
        $this->printing($data, $message, $color, $pad_length);
    }

    /**
     * @DESC         |打印消息
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
     */
    public function printing(string $data='Printing!', string $message = '', string $color = self::NOTE, int $pad_length = 0)
    {
        $doc_tmp = '【' . $message . '】：' . $this->colorize(($pad_length ? str_pad($data, $pad_length) : $data), $color);
        $doc = <<<COMMAND_LIST

$doc_tmp

COMMAND_LIST;
        exit($doc);
    }

    /**
     * @DESC         |终端输出颜色字体
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $text
     * @param string $status
     * @return string
     */
    public function colorize($text, $status): string
    {
        switch ($status) {
            case self::SUCCESS:
            case "Green":
                $this->out = "[32m"; //Green
                break;
            case self::ERROR:
            case self::FAILURE:
            case "Red":
                $this->out = "[31m"; //Red
                break;
            case self::WARNING:
            case "Yellow":
                $this->out = "[33m"; //Yellow
                break;
            case self::NOTE:
            case "Blue":
                $this->out = "[34m"; //Blue
                break;
            default:
                $this->out = "[31m"; //默认错误信息
                break;
        }
        return chr(27) . "{$this->out}" . "{$text}" . chr(27) . "[0m";
    }
}