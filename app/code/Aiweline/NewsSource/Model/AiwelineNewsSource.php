<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\NewsSource\Model;

use Weline\Framework\Database\AbstractModel;

class AiwelineNewsSource extends AbstractModel
{
    /**
     * @DESC         |存来源返回来源ID
     *
     * 参数区：
     *
     * @param string $source
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @return int
     */
    public function add(string $source): int
    {
        $exist_source = $this->where('name', '=', $source)->find();

        return $exist_source['id'] ?? $this->insert(['name' => $source]);
    }
}
