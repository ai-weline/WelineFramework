<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/21
 * 时间：12:38
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console;


use M\Framework\Output\Cli\Printing;

abstract class CommandAbstract implements CommandInterface
{

    protected Printing $printer;

//    protected \ReflectionClass $reflection_class;

    /**
     * CommandAbstract 初始函数...
     * @throws ConsoleException
     */
    function __construct()
    {
        $this->printer = new Printing();
//        try {
//            $this->reflection_class = new \ReflectionClass($this);
//        } catch (\ReflectionException $e) {
//            throw new ConsoleException('类反射错误：' . $e->getMessage());
//        }
    }


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
     * @param string $module_path
     * @param string $command
     * @return string
     */
    protected function getCommandPath(string $module_path, string $command = null): string
    {
        $command_array = explode(':', $command);
        foreach ($command_array as &$command) {
            $command = ucfirst($command);
        }
        return $module_path . '\\' . self::dir . '\\' . implode('\\', $command_array);
    }
}