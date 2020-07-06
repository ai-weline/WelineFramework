<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/28
 * 时间：15:03
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Setup\Db;


use M\Framework\Database\Db\Ddl\Table;
use M\Framework\Database\DbManager;

class Setup extends DbManager
{
    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $table_name
     * @param string $comment
     * @return Table
     */
    function createTable(string $table_name, string $comment)
    {
        return new Table($table_name, $comment);
    }
}