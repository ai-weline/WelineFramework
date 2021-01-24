<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Module\Helper;

use Weline\Framework\System\File\Scan;
use Weline\Framework\Module\Data\DirectoryInterface;

/**
 * 文件信息
 * DESC:   | 扫描模块信息
 * 作者：   秋枫雁飞
 * 日期：   2020/9/20
 * 时间：   11:02
 * 网站：   https://bbs.aiweline.com
 * Email：  aiweline@qq.com
 */
class Scanner
{
    /**
     * @var Scan
     */
    private Scan $scan;

    /**
     * @var Data
     */
    private Data $data;

    public function __construct(
        Scan $scan,
        Data $data
    ) {
        $this->scan = $scan;
        $this->data = $data;
    }

    public function getEtcFile(string $moduleName)
    {
        $moduleDir = $this->data->getModulePath($moduleName);

        return $this->scan->scanDirTree($moduleDir . DIRECTORY_SEPARATOR . DirectoryInterface::etc, 12);
    }
}
