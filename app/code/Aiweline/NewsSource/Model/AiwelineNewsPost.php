<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\NewsSource\Model;

use Weline\Framework\App\Exception;
use Weline\Framework\Database\Model;

class AiwelineNewsPost extends Model
{
    /**
     * @DESC         |存来源返回来源ID
     *
     * 参数区：
     *
     * @param array $data
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add(array $data)
    {
        if (isset($data['post_id'])) {
            $post = $this->where(['post_id' => $data['post_id']])->find();
        }
        if (isset($post)) {// 存在则更新
            $this->where(['post_id' => $post->post_id])->update($data);

            return true;
        }

        try {
            return $this->insert($data);
        } catch (Exception $exception) {
            return false;
        }
    }
}
