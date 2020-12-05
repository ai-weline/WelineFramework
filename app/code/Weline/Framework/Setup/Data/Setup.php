<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Setup\Data;

use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Setup\Db\Setup as DbSetup;

class Setup
{
    protected DbSetup $setup_db;

    protected Printing $printer;

    /**
     * Setup 初始函数...
     */
    public function __construct()
    {
        $this->printer  = new Printing();
        $this->setup_db = new DbSetup();
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return DbSetup
     */
    public function getDb()
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
    public function getPrinter(): Printing
    {
        return $this->printer;
    }
}
