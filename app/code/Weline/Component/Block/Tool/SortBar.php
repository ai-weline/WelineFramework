<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/25 00:16:53
 */

namespace Aiweline\Component\Block\Tool;

class SortBar extends \Weline\Framework\View\Block
{
    protected string $_template = 'Aiweline_Bbs::tool/sort-bar.phtml';

    public function __init()
    {
        parent::__init();
        $action = $this->request->getUri();
        $sorts  = $this->getData('sorts');
        // TODO 排序BLOCK工具
        $default_sorts = [
            'default'=>'',
            'new'=>'create_time',
        ];
    }
}