<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\NewsSource\Model;

use Weline\Framework\Database\Model;

class AiwelineNewsCategory extends Model
{
    /**
     * @DESC         |存来源返回来源ID
     *
     * 参数区：
     *
     * @param string $category
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @return int
     */
    public function add(string $category): int
    {
        $exist_category = $this->where('name', '=', $category)->find();

        return $exist_category['id'] ?? $this->insert(['name' => $category]);
    }
}
