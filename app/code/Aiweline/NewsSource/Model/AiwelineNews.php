<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\NewsSource\Model;

use Weline\Framework\Database\Model;

class AiwelineNews extends Model
{
    public function add($data)
    {
        $exist_category = $this->where('title', '=', $data['title'])
            ->where('source_id', '=', $data['source_id'])
            ->where('category_id', '=', $data['category_id'])
            ->find();

        return $exist_category['id'] ?? $this->insert($data);
    }

    /**
     * content 关联方法名
     */
    public function content()
    {
        return $this->hasOne(AiwelineNewsPost::class, 'post_id')
            ->bind(['content']);
    }

    /**
     * category 关联方法名
     */
    public function category()
    {
        return $this->hasOne(AiwelineNewsCategory::class, 'id')
            ->bind(['category' => 'name']);
    }

    /**
     * source 关联方法名
     */
    public function source()
    {
        return $this->hasOne(AiwelineNewsCategory::class, 'id')
            ->bind(['source' => 'name']);
    }
}
