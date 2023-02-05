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
use Weline\Component\ComponentInterface;

class Search extends \Weline\Framework\View\Block implements ComponentInterface
{
    protected string $_template = 'Weline_Component::form/search.phtml';

    public function __init(): void
    {
        parent::__init();
        $check_fields = ['action', 'id'];
        $data         = $this->getData();
        foreach ($check_fields as $check_field) {
            $field = $this->getData($check_field) ?: '';
            if (empty($field)) {
                throw new Exception(__('请设置搜索Block块参数：' . $field . '.示例：%1', $this->doc()));
            }
            if ($check_field === 'action') {
                $field = $this->request->isBackend() ? $this->getBackendUrl($field) : $this->getUrl($field);
            }
            $data[$check_field] = $field;
        }
        if (isset($data['template'])) {
            $this->_template = $data['template'];
        }
        $params = $this->getData('params') ?? [];
        if ($params) {
            $params = explode(',', $params);
            foreach ($params as $key=>$param) {
                unset($params[$key]);
                $params[$param]  = $this->request->getParam($param);
            }
        }
        $data['params']     = $params;
        $data['keyword']     = $data['keyword'] ?? 'keyword';
        $data['method']      = $data['method'] ?? 'GET';
        $data['placeholder'] = $data['placeholder'] ??__( '回车搜索');
        $data['value']       = $this->request->getGet($data['keyword']) ?:$data['value']??'';
        $this->assign($data);
    }

    public function doc(): string
    {
        return htmlspecialchars($this->tmp_replace('
<h3><lang>搜索组件：快速构建搜索框</lang></h3>
<block class="Weline\Component\Block\Form\Search" 
template="Weline_Component::form/search.phtml" 
cache="300" 
id="demo_search" 
action="/demo/search" 
method="get" 
keyword="keyword" 
value="Demo Keyword" 
placeholder="Please input keywords"/>

'));
    }
}
