<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database;

use Weline\Framework\Manager\ObjectManager;

abstract class Model extends AbstractModel implements ModelInterface
{
    public function __init()
    {
        parent::__init();
        $this->clear();
    }

    public function columns(): array
    {
        $cache_key = $this->getTable() . '_columns';
        if ($columns = $this->_cache->get($cache_key)) {
            return $columns;
        }
        $columns = (array)$this->query("SHOW FULL COLUMNS FROM {$this->getTable()} ")->fetch();
        $this->_cache->set($cache_key, $columns);
        return $columns;
    }
}
