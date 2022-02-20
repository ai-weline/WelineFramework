<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Backend\Config;

use Weline\Backend\Cache\BackendCache;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Config\Xml\Reader;
use Weline\Framework\Exception\Core;
use Weline\Framework\System\File\Scanner;
use Weline\Framework\Xml\Parser;

class MenuReader extends Reader
{

    function __construct(
        Scanner $scanner,
        Parser  $parser,
                $path = 'backend/menu.xml')
    {
        parent::__construct($scanner, $parser, $path);
    }

    function read(): array
    {
        $configs = parent::read();
        // 菜单提取
        $module_menus = [];
        foreach ($configs as $module_and_file => $config) {
            $m_a_f_arr = explode('::', $module_and_file);
            $module = array_shift($m_a_f_arr);
            $module_menu_file = array_pop($m_a_f_arr);
            $module_menus[$module]['file'] = $module_menu_file;
            $module_menus[$module]['data'] = [];
            if (
                !isset($config['menus']['_attribute']['noNamespaceSchemaLocation']) && (
                    'urn:weline:module:Weline_Backend::etc/xsd/menu.xsd' !== $config['menus']['_attribute']['noNamespaceSchemaLocation'])
            ) {
                $this->checkElementAttribute(
                    $config['menus'],
                    'noNamespaceSchemaLocation',
                    __('菜单元素menus必须设置：noNamespaceSchemaLocation="urn:weline:module:Weline_Backend::etc/xsd/menu.xsd"，文件：%1', $module_and_file)
                );
            }
            foreach ($config['menus'] as $menu) {
                if (isset($menu['add'])) {
                    if (is_int(array_key_first($menu['add']))) {
                        foreach ($menu['add'] as $menu_data) {
                            if (isset($menu_data['_attribute'])) {
                                $this->checkElementAttribute(
                                    $menu_data,
                                    'name',
                                    __('菜单配置错诶：add元素缺少name属性,文件：%1 配置示例： <add source="Weline_Backend::dashboard" name="Dashboard" title="Dashboard" action="/" icon="mdi-home-variant-outline"/>', $module_and_file)
                                );
                                $this->checkElementAttribute(
                                    $menu_data,
                                    'source',
                                    __('菜单配置错诶：add元素缺少source属性,文件：%1 配置示例： <add source="Weline_Backend::dashboard" name="Dashboard" title="Dashboard" action="/" icon="mdi-home-variant-outline"/>', $module_and_file)
                                );
                                $this->checkElementAttribute(
                                    $menu_data,
                                    'title',
                                    __('菜单配置错诶：add元素缺少title属性,文件：%1 配置示例： <add source="Weline_Backend::dashboard" name="Dashboard" title="Dashboard" action="/" icon="mdi-home-variant-outline"/>', $module_and_file)
                                );
                                $this->checkElementAttribute(
                                    $menu_data,
                                    'action',
                                    __('菜单配置错诶：add元素缺少action属性,文件：%1 配置示例： <add source="Weline_Backend::dashboard" name="Dashboard" title="Dashboard" action="/" icon="mdi-home-variant-outline"/>', $module_and_file)
                                );
                                $this->checkElementAttribute(
                                    $menu_data,
                                    'icon',
                                    __('菜单配置错诶：add元素缺少icon属性,文件：%1 配置示例： <add source="Weline_Backend::dashboard" name="Dashboard" title="Dashboard" action="/" icon="mdi-home-variant-outline"/>', $module_and_file)
                                );
                                $module_menus[$module]['data'][] = $menu_data['_attribute'];
                            }
                        }
                    } else {
                        $this->checkElementAttribute(
                            $menu['add'],
                            'name',
                            __('菜单配置错诶：add元素缺少name属性,文件：%1 配置示例： <add source="Weline_Backend::dashboard" name="Dashboard" title="Dashboard" action="/"/>', $module_and_file)
                        );
                        $this->checkElementAttribute(
                            $menu['add'],
                            'source',
                            __('菜单配置错诶：add元素缺少source属性,文件：%1 配置示例： <add source="Weline_Backend::dashboard" name="Dashboard" title="Dashboard" action="/"/>', $module_and_file)
                        );
                        $this->checkElementAttribute(
                            $menu['add'],
                            'title',
                            __('菜单配置错诶：add元素缺少title属性,文件：%1 配置示例： <add source="Weline_Backend::dashboard" name="Dashboard" title="Dashboard" action="/"/>', $module_and_file)
                        );
                        $this->checkElementAttribute(
                            $menu['add'],
                            'action',
                            __('菜单配置错诶：add元素缺少action属性,文件：%1 配置示例： <add source="Weline_Backend::dashboard" name="Dashboard" title="Dashboard" action="/"/>', $module_and_file)
                        );
                        $this->checkElementAttribute(
                            $menu['add'],
                            'icon',
                            __('菜单配置错诶：add元素缺少icon属性,文件：%1 配置示例： <add source="Weline_Backend::dashboard" name="Dashboard" title="Dashboard" action="/" icon="mdi-home-variant-outline"/>', $module_and_file)
                        );
                        if ($menu['add']['_attribute']) $module_menus[$module]['data'][] = $menu['add']['_attribute'];
                    }

                }
            }
        }
        return $module_menus;
    }
}