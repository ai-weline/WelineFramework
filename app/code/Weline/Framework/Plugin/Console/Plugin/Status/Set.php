<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/3/1
 * 时间：0:17
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Plugin\Console\Plugin\Status;


class Set implements \Weline\Framework\Console\CommandInterface
{

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        p($args);
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return __('状态操作：0/1 0:关闭，1:启用');
    }
}