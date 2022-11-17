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
    protected string $_template = 'Weline_Component::tool/sort-bar.phtml';

    public function __init()
    {
        parent::__init();
        $action        = $this->request->getUrlBuilder()->getUrl();
        $action_sorts  = $this->getData('sorts');#lasted=>create_time:desc
        $up_icon       = $this->getData('up_icon') ?: '<i class="mdi mdi-arrow-up"></i>';
        $down_icon     = $this->getData('down_icon') ?: '<i class="mdi mdi-arrow-down"></i>';
        $default_sorts = [
            'default' => 'create_time:desc',
            'lasted'  => 'create_time:desc',#create_time:desc
            'update'  => 'update_time:desc',
        ];
        $sorts         = $default_sorts;
        if (is_string($action_sorts)) {
            $action_sorts = explode(',', $action_sorts);
            foreach ($action_sorts as $key => $action_sort) {
                unset($action_sorts[$key]);
                $action_sort = explode('=>', $action_sort);
                if (count($action_sort) === 2) {
                    $action_sorts[$action_sort[0]] = $action_sort[1];
                }
            }
            if ($action_sorts) {
                $sorts = array_merge($default_sorts, $action_sorts);
            }
        } elseif (is_array($action_sorts)) {
            $sorts = array_merge($default_sorts, $action_sorts);
        }

        # params排序反转
        $params     = $this->request->getParams();
        $target     = false;
        foreach ($sorts as $key => $sort) {
            if (is_array($sort)) {
                break;
            }
            $icon       = $down_icon;
            $sort = str_replace('%3A', ':', $sort);
            $str_len = strlen($sort);
            if (isset($params[$key])) {
                $sort = str_replace('%3A', ':', $params[$key]);
                $str_len = strlen($sort);
                $target = true;
                if (str_ends_with(strtolower($sort), ':desc')) {
                    $sort = substr($sort, 0, $str_len - 5) . ':asc';
                    $icon = $up_icon;
                } elseif (str_ends_with(strtolower($sort), ':asc')) {
                    $sort = substr($sort, 0, $str_len - 4) . ':desc';
                } else {
                    $sort = $sort . ':desc';
                }
            } elseif ($pos = strrpos($sort, ':')) {
                if ($pos === ($str_len - 1)) {
                    $sort = $sort . ':desc';
                } else {
                    if (str_ends_with(strtolower($sort), ':asc')) {
                        $icon = $up_icon;
                    }
                }
            } else {
                $sort = $sort . ':desc';
            }
            $sorts[$key] = [
                'href'   => $this->request->getUrlBuilder()->extractedUrl([$key => $sort], true),
                'name'   => __($key),
                'tag'    => $sort,
                'icon'   => $icon,
                'target' => $target,
            ];
        }
//        d($params);
//        dd($sorts);

        $this->assign('sorts', $sorts);
        $this->assign('id', md5($action) . Encrypt::get_rand_number_code());
        $this->assign('action', $action);
    }
}
