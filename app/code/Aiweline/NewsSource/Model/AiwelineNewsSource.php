<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/17
 * 时间：21:05
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\NewsSource\Model;


use M\Framework\Database\Model;

class AiwelineNewsSource extends Model
{
    /**
     * @DESC         |存来源返回来源ID
     *
     * 参数区：
     *
     * @param string $source
     * @return int
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    function add(string $source): int
    {
        $exist_source = $this->where('name', '=', $source)->find();
        return isset($exist_source['id']) ? $exist_source['id'] : $this->insert(['name' => $source]);
    }
}