<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/10/24 23:00:45
 */

namespace Weline\Component\Block\Form;

use PHPUnit\Framework\Exception;

class Search extends \Weline\Framework\View\Block
{
    protected string $_template = 'Weline_Component::form/search.phtml';

    public function __init()
    {
        parent::__init();
        $check_fields = ['action', 'id'];
        $data         = [];
        foreach ($check_fields as $check_field) {
            $field = $this->getData($check_field) ?: '';
            if (empty($field)) {
                throw new Exception(__('请设置搜索Block块参数：' . $field . '.示例：%1', htmlspecialchars('<block class="Weline/Demo/Block/Search" cache="300" id="demo_search" action="/demo/search" method="get" keyword="keyword" value="Demo Keyword" placeholder="Please input keywords"/>')));
            }
            if ($check_field === 'action') {
                $field = $this->request->getUrlBuilder()->getUrl($field);
            }
            $data[$check_field] = $field;
        }
        $data['keyword']     = $data['keyword'] ?? 'keyword';
        $data['method']      = $data['method'] ?? 'GET';
        $data['placeholder'] = __($data['placeholder'] ?? '回车搜索');
        $data['value']       = __($this->request->getGet($data['keyword']) ?: $data['value'] ?? '');
        $this->assign($data);
    }
}
