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

namespace Weline\Component\Block\Tool;

use Weline\Framework\System\Security\Encrypt;

class SortBar extends \Weline\Framework\View\Block
{
    public const default_sorts = [
        'default' => [
            'field' => 'create_time',
            'sort'  => 'desc'
        ],
        'lasted'  => [
            'field' => 'create_time',
            'sort'  => 'desc'
        ],
        'update'  => [
            'field' => 'update_time',
            'sort'  => 'desc'
        ]
    ];
    protected string $_template = 'Weline_Component::tool/sort-bar.phtml';

    public function __init()
    {
        parent::__init();
        $action = $this->request->getUrlBuilder()->getUrl();
        $filter = $this->getData('sorter');# 排序器名字，用于读取映射缓存
        if (empty($filter)) {
            throw new \Exception(__('排序器ID不能为空：%1', htmlentities('<block class="">')));
        }
        # 查看排序器缓存
        $sorts = $this->_cache->get($filter) ?: [];
        if (!$sorts) {
            $action_sorts       = $this->getData('sorts');#lasted=>create_time:desc
            $up_icon            = $this->getData('up_icon') ? '<i class="' . $this->getData('up_icon') . '"></i>' : '<i class="mdi mdi-arrow-up"></i>';
            $down_icon          = $this->getData('down_icon') ? '<i class="' . $this->getData('down_icon') . '"></i>' : '<i class="mdi mdi-arrow-down"></i>';
            $sorts['up_icon']   = $up_icon;
            $sorts['down_icon'] = $down_icon;
            $sorts['data']      = self::default_sorts;
            if ($action_sorts) {
                $action_sorts = explode(',', $action_sorts);
                foreach ($action_sorts as $key => $action_sort) {
                    unset($action_sorts[$key]);
                    $action_sort = explode('=>', $action_sort);
                    if (count($action_sort) === 2) {
                        $action_sort_value             = explode(':', $action_sort[1]);
                        $action_sorts[$action_sort[0]] = [
                            'field' => $action_sort_value[0],
                            'sort'  => $action_sort_value[1] ?? 'desc',
                        ];
                    }
                }
                if ($action_sorts) {
                    $sorts['data'] = array_merge($sorts['data'], $action_sorts);
                }
            }
            $this->_cache->set($filter, $sorts);
        }

        # params排序反转
        $params    = $this->request->getParams();
        $sort_data = [];
        foreach ($sorts['data'] as $key => $sort) {
            $icon      = $sorts['down_icon'];
            $target = isset($params[$key]);
            if ($target) {
                $sort['sort'] = $params[$key];
            }
            switch (strtolower($sort['sort'])) {
                case 'desc':
                    $sort['sort'] = 'asc';
                    break;
                case 'asc':
                    $sort['sort'] = 'desc';
                    $icon         = $sorts['up_icon'];
                    break;
            }
            $sort_data[$key] = [
                'href'   => $this->request->getUrlBuilder()->extractedUrl([$key => $sort['sort']], true),
                'name'   => __($key),
                'icon'   => $icon,
                'sort'   => $sort['sort'],
                'target' => $target,
            ];
        }
        $this->assign('sorts', $sort_data);
        $this->assign('action', $action);
    }
}
