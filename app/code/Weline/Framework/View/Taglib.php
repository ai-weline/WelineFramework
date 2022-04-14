<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\View;

use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\View\Exception\TemplateException;

class Taglib
{
    public function getTags(Template $template, string $fileName = ''): array
    {
        return [
            'php'         => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => '<?php ',
                            'tag-end'   => '?>',
                            default     => "<?php {$tag_data[1]} ?>"
                        };
                    }
            ],
            'w-php'       => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => '<?php ',
                            'tag-end'   => '?>',
                            default     => "<?php {$tag_data[1]} ?>"
                        };
                    }
            ],
            'include'     => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => '<?php include(',
                            'tag-end'   => ');?>',
                            default     => "<?php include({$tag_data[1]});?>"
                        };
                    }
            ],
            'w-include'   => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => '<?php include(',
                            'tag-end'   => ');?>',
                            default     => "<?php include({$tag_data[1]});?>"
                        };
                    }
            ],
            'var'         => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => '<?= ',
                            'tag-end'   => '?>',
                            default     => "<?={$tag_data[1]}?>"
                        };
                    }
            ],
            'w-var'       => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => '<?= ',
                            'tag-end'   => '?>',
                            default     => "<?={$tag_data[1]}?>"
                        };
                    }
            ],
            'pp'          => [
                'tag'      => 1,/*
                'tag-start' => 1,
                'tag-end'   => 1,*/
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        switch ($tag_key) {
                            case '@tag{}':
                            case '@tag()':
                                $var_name = $tag_data[1];
                                if (!str_starts_with($var_name, '$')) {
                                    $var_name .= '$' . $var_name;
                                }
                                return "<?=p({$var_name})?>";
                            case 'tag-start':
                                return '<?=p(';
                            case 'tag-end':
                                return ')?>';
                            default:
                                $var_name = $tag_data[2];
                                if (!str_starts_with($var_name, '$')) {
                                    $var_name = '$' . $var_name;
                                }
                                return "<?=p({$var_name})?>";
                        }
                    }],
            'w-pp'        => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        switch ($tag_key) {
                            case '@tag{}':
                            case '@tag()':
                                $var_name = $tag_data[1];
                                if (!str_starts_with($var_name, '$')) {
                                    $var_name .= '$' . $var_name;
                                }
                                return "<?=p({$var_name})?>";
                            case 'tag-start':
                                return '<?=p(';
                            case 'tag-end':
                                return ')?>';
                        }
                        return '';
                    }],
            'if'          => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'attr'      => ['condition' => 1],
                'callback'  => function ($tag_key, $config, $tag_data, $attributes) {
                    $result = '';
                    switch ($tag_key) {
                        // @if{$a === 1=><li><var>$a</var></li>|$a===2=><li><var>$a</var></li>}
                        case '@tag{}':
                        case '@tag()':
                            $content_arr = explode('|', $tag_data[1]);
                            foreach ($content_arr as &$item) {
                                $item = explode('=>', $content_arr[0]);
                            }
                            if (1 === count($content_arr)) {
                                $result = "<?php if({$content_arr[0][0]}):{$content_arr[0][1]};endif;?>";
                            }
                            if (1 < count($content_arr)) {
                                $result = "<?php if({$content_arr[0][0]}):{$content_arr[0][1]};endif;?>";
                            }
                            foreach ($content_arr as $key => $data) {
                                if (0 === $key) {
                                    $result = "<?php if($data[0]):?>" . $data[1];
                                } else {
                                    if (count($data) > 1) {
                                        $result .= "<?php elseif($data[0]):?>" . $data[1];
                                    } else {
                                        $result .= '<?php else:?>' . $data[0];
                                    }
                                }
                            }
                            $result .= '<?php endif;?>';
                            break;
                        case 'tag-self-close':
                            throw new TemplateException(__('if没有自闭合标签。示例：%1', '<if condition="$a>$b"><var>a</var><elseif condition="$b>$a"/><var>b</var><else/><var>a</var><var>b</var></if>'));
                        case 'tag-start':
                            $condition = $attributes['condition'];
                            $result    = "<?php if({$condition}):?>";
                            break;
                        case 'tag-end':
                            $result = '<?php endif;?>';
                            break;
                        default:
                    }
                    return $result;
                }],
            'w-if'        => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'attr'      => ['condition' => 1],
                'callback'  => function ($tag_key, $config, $tag_data, $attributes) {
                    $result = '';
                    switch ($tag_key) {
                        // @if{$a === 1=><li><var>$a</var></li>|$a===2=><li><var>$a</var></li>}
                        case '@tag{}':
                        case '@tag()':
                            $content_arr = explode('|', $tag_data[1]);
                            foreach ($content_arr as &$item) {
                                $item = explode('=>', $content_arr[0]);
                            }
                            if (1 === count($content_arr)) {
                                $result = "<?php if({$content_arr[0][0]}):{$content_arr[0][1]};endif;?>";
                            }
                            if (1 < count($content_arr)) {
                                $result = "<?php if({$content_arr[0][0]}):{$content_arr[0][1]};endif;?>";
                            }
                            foreach ($content_arr as $key => $data) {
                                if (0 === $key) {
                                    $result = "<?php if($data[0]):?>" . $data[1];
                                } else {
                                    if (count($data) > 1) {
                                        $result .= "<?php elseif($data[0]):?>" . $data[1];
                                    } else {
                                        $result .= '<?php else:?>' . $data[0];
                                    }
                                }
                            }
                            $result .= '<?php endif;?>';
                            break;
                        case 'tag-self-close':
                            throw new TemplateException(__('if没有自闭合标签。示例：%1', '<if condition="$a>$b"><var>a</var><elseif condition="$b>$a"/><var>b</var><else/><var>a</var><var>b</var></if>'));
                        case 'tag-start':
                            $condition = $attributes['condition'];
                            $result    = "<?php if({$condition}):?>";
                            break;
                        case 'tag-end':
                            $result = '<?php endif;?>';
                            break;
                        default:
                    }
                    return $result;
                }],
            'empty'       => [
                'tag'      => 1,
                'tag-end'  => 1,
                'callback' => function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                    switch ($tag_key) {
                        // @empty{$name|<li>空的</li>}
                        case '@tag{}':
                        case '@tag()':
                            $content_arr = explode('|', $tag_data[1]);
                            return '<?php if(empty($template->getData(\'' . $content_arr[0] . '\')))echo \'' . $template->tmp_replace(trim($content_arr[1] ?? '')) . '\'?>';
                        case 'tag':
                            if (isset($attributes['name'])) {
                                throw new TemplateException(__('empty标签需要设置name属性！例如：<empty name="catalogs"><li>没有数据</li></empty>'));
                            }
                            return "<?php if(empty(\$template->getData('{$attributes['name']}'))): ?>";
                        case 'tag-end':
                            return '<?php endif; ?>';
                        default:
                            return '';
                    }
                }
            ],
            'elseif'      => [
                'attr'           => ['condition' => 1],
                'tag-self-close' => 1,
                'callback'       =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        $result = '';
                        switch ($tag_key) {
                            // @if{$a === 1=><li><var>$a</var></li>|$a===2=><li><var>$a</var></li>}
                            case '@tag{}':
                            case '@tag()':
                                throw new TemplateException(__('elseif没有@elseif()和@elseif{}用法。示例：%1', '<if condition="$a>$b"><var>a</var><elseif condition="$b>$a"/><var>b</var><else/><var>a</var><var>b</var></if>'));
                            case 'tag-self-close':
                                $condition = $attributes['condition'];
                                $result    = "<?php elseif({$condition}):?>";
                                break;
                            default:
                        }
                        return $result;
                    }],
            'else'        => [
                'tag-self-close' => 1,
                'callback'       =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        $result = '';
                        switch ($tag_key) {
                            // @if{$a === 1=><li><var>$a</var></li>|$a===2=><li><var>$a</var></li>}
                            case '@tag{}':
                            case '@tag()':
                                throw new TemplateException(__('elseif没有@elseif()和@elseif{}用法。示例：%1', '<if condition="$a>$b"><var>a</var><elseif condition="$b>$a"/><var>b</var><else/><var>a</var><var>b</var></if>'));
                            case 'tag-self-close':
                                $result = '<?php else:?>';
                                break;
                            default:
                        }
                        return $result;
                    }],
            'block'       => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => ObjectManager::getInstance(trim($tag_data[2]))->render(),
                            default => ObjectManager::getInstance(trim($tag_data[1]))->render()
                        };
                    }
            ],
            'w-block'     => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => ObjectManager::getInstance(trim($tag_data[2]))->render(),
                            default => ObjectManager::getInstance(trim($tag_data[1]))->render()
                        };
                    }
            ],
            'foreach'     => [
                'attr'      => ['name' => 1, 'key' => 0, 'item' => 0],
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  => function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                    switch ($tag_key) {
                        // @foreach{$name as $key=>$v|<li><var>$k</var>:<var>$v</var></li>}
                        case '@tag{}':
                        case '@tag()':
                            $content_arr = explode('|', $tag_data[1]);
                            return "<?php
                        foreach({$content_arr[0]}){
                        ?>
                            {$template->tmp_replace($content_arr[1]??'')}
                            <?php
                        }
                        ?>";
                        case 'tag-self-close':
                            throw new TemplateException(__('foreach没有自闭合标签。示例：%1', '<foreach name="catalogs" key="key" item="v"><li><var>name</var></li></foreach>'));
                        case 'tag-start':
                            if (!isset($attributes['item'])) {
                                $attributes['item'] = 'v';
                            }
                            if (!isset($attributes['name'])) {
                                throw new TemplateException(__('foreach标签需要指定要循环的变量name属性。例如：需要循环catalogs变量则%1', '<foreach name="catalogs" key="key" item="v"><li><var>name</var></li></foreach>'));
                            }
                            foreach ($attributes as $key => $attribute) {
                                if (!str_starts_with($attribute, '$')) {
                                    $attributes[$key] = '$' . $attribute;
                                }
                            }
                            $vars = $attributes['name'];
                            $k_i  = isset($attributes['key']) ? $attributes['key'] . ' => ' . $attributes['item'] : $attributes['item'];
                            return "<?php foreach($vars as $k_i):?>";
                        case 'tag-end':
                            return '<?php endforeach;?>';
                        default:
                            return '';
                    }
                }
            ],
            'w-foreach'   => [
                'attr'      => ['name' => 1, 'key' => 0, 'item' => 0],
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  => function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                    switch ($tag_key) {
                        // @foreach{$name as $key=>$v|<li><var>$k</var>:<var>$v</var></li>}
                        case '@tag{}':
                        case '@tag()':
                            $content_arr = explode('|', $tag_data[1]);
                            return "<?php
                        foreach({$content_arr[0]}){
                        ?>
                            {$template->tmp_replace($content_arr[1]??'')}
                            <?php
                        }
                        ?>";
                        case 'tag-self-close':
                            throw new TemplateException(__('foreach没有自闭合标签。示例：%1', '<foreach name="catalogs" key="key" item="v"><li><var>name</var></li></foreach>'));
                        case 'tag-start':
                            if (!isset($attributes['item'])) {
                                $attributes['item'] = 'v';
                            }
                            if (!isset($attributes['name'])) {
                                throw new TemplateException(__('foreach标签需要指定要循环的变量name属性。例如：需要循环catalogs变量则%1', '<foreach name="catalogs" key="key" item="v"><li><var>name</var></li></foreach>'));
                            }
                            foreach ($attributes as $key => $attribute) {
                                if (!str_starts_with($attribute, '$')) {
                                    $attributes[$key] = '$' . $attribute;
                                }
                            }
                            $vars = $attributes['name'];
                            $k_i  = isset($attributes['key']) ? $attributes['key'] . ' => ' . $attributes['item'] : $attributes['item'];
                            return "<?php foreach($vars as $k_i):?>";
                        case 'tag-end':
                            return '<?php endforeach;?>';
                        default:
                            return '';
                    }
                }
            ],
            'static'      => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'   => $template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2])),
                            default => $template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))
                        };
                    }
            ],
            'w-static'    => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'   => $template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2])),
                            default => $template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))
                        };
                    }
            ],
            'template'    => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'   => file_get_contents($template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($tag_data[2]))),
                            default => file_get_contents($template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($tag_data[1])))
                        };
                    }
            ],
            'w-template'  => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'   => file_get_contents($template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($tag_data[2]))),
                            default => file_get_contents($template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($tag_data[1])))
                        };
                    }
            ],
            'js'          => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'   => "<script {$tag_data[1]} src='{$template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2]))}'></script>",
                            default => "<script src='{$template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))}'></script>"
                        };
                    }
            ],
            'w-js'        => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'   => "<script {$tag_data[1]} src='{$template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2]))}'></script>",
                            default => "<script src='{$template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))}'></script>"
                        };
                    }
            ],
            'css'         => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'   => "<link {$tag_data[1]} href='{$template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2]))}' rel=\"stylesheet\" type=\"text/css\"/>",
                            default => "<link href='{$template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))}' rel=\"stylesheet\" type=\"text/css\"/>"
                        };
                    }
            ],
            'w-css'       => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'   => "<link {$tag_data[1]} href='{$template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2]))}' rel=\"stylesheet\" type=\"text/css\"/>",
                            default => "<link href='{$template->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))}' rel=\"stylesheet\" type=\"text/css\"/>"
                        };
                    }
            ],
            'lang'        => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'       => "<?=__('{$tag_data[2]}')?>",
                            'tag-start' => "<?=__('",
                            'tag-end'   => "')?>",
                            default     => "<?=__('{$tag_data[1]}')?>"
                        };
                    }
            ],
            'w-lang'      => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'       => "<?=__('{$tag_data[2]}')?>",
                            'tag-start' => "<?=__('",
                            'tag-end'   => "')?>",
                            default     => "<?=__('{$tag_data[1]}')?>"
                        };
                    }
            ],
            'url'         => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        $result = '';
                        switch ($tag_key) {
                            case 'tag':
                                $data   = trim($tag_data[2], '\'"');
                                $result .= "<?=\$this->getUrl('{$data}')?>";
                                break;
                            case  'tag-start':
                                $result .= "<?=\$this->getUrl('";
                                break;
                            case 'tag-end':
                                $result .= "')?>";
                                break;
                            default:
                                $data   = trim($tag_data[1], '\'"');
                                $result .= "<?=\$this->getUrl('{$data}')?>";
                        };
                        return $result;
                    }
            ],
            'w-url'       => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'       => "<?=\$this->getUrl({$tag_data[2]})?>",
                            'tag-start' => "<?=\$this->getUrl('",
                            'tag-end'   => "')?>",
                            default     => "<?=\$this->getUrl({$tag_data[1]})?>"
                        };
                    }
            ],
            'admin-url'   => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'       => "<?=\$this->getAdminUrl({$tag_data[2]})?>",
                            'tag-start' => "<?=\$this->getAdminUrl('",
                            'tag-end'   => "')?>",
                            default     => "<?=\$this->getAdminUrl({$tag_data[1]})?>"
                        };
                    }
            ],
            'w-admin-url' => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) use ($template) {
                        return match ($tag_key) {
                            'tag'       => "<?=\$this->getAdminUrl({$tag_data[2]})?>",
                            'tag-start' => "<?=\$this->getAdminUrl('",
                            'tag-end'   => "')?>",
                            default     => "<?=\$this->getAdminUrl({$tag_data[1]})?>"
                        };
                    }
            ],
            'hook'        => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => "<?=\$this->getHook('" . trim($tag_data[2]) . "')?>",
                            default => "<?=\$this->getHook('" . trim($tag_data[1]) . "')?>"
                        };
                    }
            ],
        ];
    }

    public function tagReplace(Template &$template, string &$content, string &$fileName = '')
    {
        # 系统自带的标签
        $tags = $this->getTags($template, $fileName);
        /**@var EventsManager $event */
        $event = ObjectManager::getInstance(EventsManager::class);
        $data  = (new DataObject(['template' => $template, 'tags' => $tags, 'content' => $content]));
        $event->dispatch('Framework_Template::after_tags_config', ['data' => $data]);
        $tags = $data->getData('tags');

        foreach ($tags as $tag => $tag_configs) {
            $tag_patterns        = [
                'tag'            => '/<' . $tag . '([\s\S]*?)>([\s\S]*?)<\/' . $tag . '>/m',
                'tag-start'      => '/<' . $tag . '([\s\S]*?)>/m',
                'tag-end'        => '/<\/' . $tag . '>/m',
                'tag-self-close' => '/<' . $tag . '([\s\S]*?)\/>/m',
                '@tag()'         => '/\@' . $tag . '\(([\s\S]*?)\)/m',
                '@tag{}'         => '/\@' . $tag . '\{([\s\S]*?)\}/m',
            ];
            $tag_config_patterns = [];
            foreach ($tag_configs as $config_name => $tag_config) {
                if (str_starts_with($config_name, 'tag') && $tag_config) {
                    $tag_config_patterns[$config_name] = $tag_patterns[$config_name];
                }
            }
            # 默认匹配@tag()和@tag{}
            $tag_config_patterns['@tag()'] = $tag_patterns['@tag()'];
            $tag_config_patterns['@tag{}'] = $tag_patterns['@tag{}'];

            # 标签验证测试
//            if('var'===$tag){
//                foreach ($tag_config_patterns as &$tag_config_pattern) {
//                    $tag_config_pattern = htmlentities($tag_config_pattern);
//                }
//                p($tag_config_patterns);
//            }
            # 匹配处理
            $format_function = $tag_configs['callback'];
            foreach ($tag_config_patterns as $tag_key => $tag_pattern) {
                preg_match_all($tag_pattern, $content, $customTags, PREG_SET_ORDER);
                foreach ($customTags as $customTag) {
                    $originalTag   = $customTag[0];
                    $rawAttributes = $customTag[1] ?? '';
                    # 标签支持匹配->
                    if (!in_array($tag_key, ['@tag()', '@tag{}'])) {
                        $rawAttributes = rtrim($rawAttributes, '"');
                        $rawAttributes = rtrim($rawAttributes, '\'');
                        if (is_int(strrpos($rawAttributes, '\''))) {
                            $rawAttributes .= '\'';
                        }
                        if (is_int(strrpos($rawAttributes, '"'))) {
                            $rawAttributes .= '"';
                        }
                    }
                    $customTag[1]       = $rawAttributes;
                    $formatedAttributes = array();
                    # 兼容：属性值双引号
                    preg_match_all('/([^=]+)=\"([^\"]+)\"/', $rawAttributes, $attributes, PREG_SET_ORDER);
                    foreach ($attributes as $attribute) {
                        if (isset($attribute[2])) {
                            $formatedAttributes[trim($attribute[1])] = trim($attribute[2]);
                        }
                    }
                    # 兼容：属性值单引号
                    preg_match_all('/([^=]+)=\'([^\']+)\'/', $rawAttributes, $attributes, PREG_SET_ORDER);
                    foreach ($attributes as $attribute) {
                        if (isset($attribute[2])) {
                            $formatedAttributes[trim($attribute[1])] = trim($attribute[2]);
                        }
                    }
                    # 验证标签属性
                    $attrs = $tag_configs['attr'] ?? [];
                    if ($attrs && ('tar-start' === $tag_key || 'tag-self-close' === $tag_key)) {
                        $attributes_keys = array_keys($formatedAttributes);
                        foreach ($attrs as $attr => $required) {
                            if ($required && !in_array($attr, $attributes_keys)) {
                                $provide_attr = implode(',', $attributes_keys);
                                throw new TemplateException(__('%1:标签必须设置属性%2, 提供的属性：3% 文件：%4', [$tag, $attr, $provide_attr, $fileName]));
                            }
                        }
                    }
                    $content = str_replace($originalTag, $format_function($tag_key, $tag_configs, $customTag, $formatedAttributes), $content);
                }
            }
        }
        return $content;
    }
}
