<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/17
 * 时间：21:06
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\NewsSource\Model;


use M\Framework\Database\Model;

class AiwelineNews extends Model
{
    function add($data)
    {
        $exist_category = $this->where('title', '=', $data['title'])
            ->where('source_id', '=', $data['source_id'])
            ->where('category_id', '=', $data['category_id'])
            ->find();
        return isset($exist_category['id']) ? $exist_category['id'] : $this->insert($data);
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
            ->bind(['category'=>'name']);
    }
    /**
     * source 关联方法名
     */
    public function source()
    {
        return $this->hasOne(AiwelineNewsCategory::class, 'id')
            ->bind(['source'=>'name']);
    }
}