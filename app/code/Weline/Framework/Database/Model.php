<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;


class Model extends AbstractModel implements ModelInterface
{
    protected function processTable(): string
    {
        if (empty($this->table = $this->getTable())) {
            parent::processTable();
        }
        return $this->table;
    }

    function getTable(): string
    {
        return '';
    }

    function getPrimaryKey(): string
    {
        return 'id';
    }

    function getFields(): array
    {
        return array();
    }
}