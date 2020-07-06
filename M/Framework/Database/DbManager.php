<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/2
 * 时间：1:04
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Database;


use M\Framework\App\Etc;

/**
 * 文件信息
 * DESC:   |
 * 作者：   秋枫雁飞
 * 日期：   2020/7/2
 * 时间：   1:24
 * 网站：   https://bbs.aiweline.com
 * Email：  aiweline@qq.com
 */
class DbManager extends \think\DbManager
{
    function __construct()
    {
        $this->setConfig(Etc::getInstance()->getDbConfig());
        parent::__construct();
    }

    /**
     * @DESC         |补充查询
     *
     * 参数区：
     *
     * @param string $sql
     * @return mixed
     */
    function query(string $sql)
    {
        return parent::query($sql);
    }
}