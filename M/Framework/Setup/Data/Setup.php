<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/28
 * 时间：13:23
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Setup\Data;


use M\Framework\App\Exception;
use M\Framework\Database\Db;
use M\Framework\Output\Cli\Printing;
use M\Framework\Setup\Db\Setup as DbSetup;

class Setup
{
    protected DbSetup $setup_db;
    protected Printing $printer;

    /**
     * Setup 初始函数...
     */
    function __construct()
    {
        $this->printer = new Printing();
        $this->setup_db = new DbSetup();
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return DbSetup
     */
    function getDb()
    {
        return $this->setup_db;
    }

    /**
     * @DESC         |方法描述
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