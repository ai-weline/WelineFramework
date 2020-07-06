<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/28
 * 时间：13:14
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Setup\Data;


use M\Framework\App\Etc;
use M\Framework\Output\Cli\Printing;

class Context
{
    private Printing $printer;
    private array $modules;
    protected string $module_name;
    protected string $module_version;
    protected string $module_description;


    /**
     * Context 初始函数...
     * @param string $module_name
     * @param string $module_version
     * @param string $module_description
     */
    function __construct(string $module_name, string $module_version, string $module_description = '')
    {
        $this->module_name = $module_name;
        $this->module_version = $module_version;
        $this->module_description = $module_description;
        $this->modules = Etc::getInstance()->getModuleList();
        $this->printer = new Printing();
    }

    /**
     * @DESC         |读取要升级的模块版本
     *
     * 参数区：
     *
     * @return bool|mixed
     */
    function getVersion()
    {
        return isset($this->modules[$this->module_name]['version']) ? $this->modules[$this->module_name]['version'] : false;
    }

    /**
     * @DESC         |返回新的版本号
     *
     * 参数区：
     *
     * @return string
     */
    function getNewVersion(): string
    {
        return $this->module_version;
    }

    /**
     * @DESC         |返回模块描述
     *
     * 参数区：
     *
     * @return string
     */
    function getDescription(): string
    {
        return isset($this->modules[$this->module_name]['description']) ? $this->modules[$this->module_name]['description'] : '';
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return string
     */
    function getNewDescription(): string
    {
        return $this->module_description;
    }

    /**
     * @DESC         |模块是否被禁用
     *
     * 参数区：
     *
     * @return bool
     */
    function isDisable()
    {
        $module_status = $this->modules[$this->module_name]['status'];
        return $module_status ? false : true;
    }

    /**
     * @DESC         |获取打印助手
     *
     * 参数区：
     *
     * @return Printing
     */
    function getPrinter(): Printing
    {
        return $this->printer;
    }
}