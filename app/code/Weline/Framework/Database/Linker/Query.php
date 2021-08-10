<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Linker;


use Weline\Framework\Database\Model;

abstract class Query implements QueryInterface
{
    private \PDO $link;

    function __construct(
        \PDO $link
    ){

        $this->link = $link;
    }

    /**
     * @return \PDO
     */
    public function getLink(): \PDO
    {
        return $this->link;
    }

    /**
     * @param \PDO $link
     */
    public function setLink(\PDO $link): void
    {
        $this->link = $link;
    }

}