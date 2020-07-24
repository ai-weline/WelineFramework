<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/17
 * 时间：21:07
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\NewsSource\Model;


use M\Framework\App\Exception;
use M\Framework\Database\Model;

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
    function add(array $data)
    {
        if (isset($data['post_id'])) {
            $post = $this->where(['post_id' => $data['post_id']])->find();
        }
        if (isset($post)) {// 存在则更新
            $this->where(['post_id' => $post->post_id])->update($data);
            return true;
        } else {
            try {
                return $this->insert($data);
            } catch (Exception $exception) {
                return false;
            }
        }
    }
}