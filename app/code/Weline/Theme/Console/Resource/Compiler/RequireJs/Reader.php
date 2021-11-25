<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console\Resource\Compiler\RequireJs;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\System\File\Data\File;
use Weline\Framework\View\Template;

class Reader extends \Weline\Theme\Config\StaticsReader
{
    private string $file;

    function __init()
    {
        parent::__init();
        $this->path = 'view';
        $this->file = 'require.config.js';
    }

    function setFile(string $file)
    {
        $this->file = $file;
        return $this;
    }

    function parserRequireConfigs()
    {
        # require js 配置
        $require_configs = $this->getFileList();
        $module_list = Env::getInstance()->getModuleList();
        foreach ($require_configs as $require_config_key => $require_config_js) {
            $area = $require_config_js['area'];
            $require_file = fopen($require_config_js['origin'], 'r');
            # 删除注释
            $require_file_content = '';
            while (!feof($require_file)) {
                $line = fgets($require_file);
                if (!is_int(strpos($line, '//'))) {
                    $require_file_content .= $line;
                }
            }
//            if(1===$require_config_key)p($require_file_content);
            $config_params_str = substr($require_file_content, strpos($require_file_content, 'config('));
            $start = strpos($config_params_str, 'config(') + 7;
            $end = strpos($config_params_str, ")");
            $config_params_str = substr($config_params_str, $start, $end - $start);
            $config_params_str = trim($config_params_str);
            $config_params_str = trim($config_params_str, '{');
            $config_params_str = rtrim($config_params_str, '}');
            $config_params_str_arr = explode(',', $config_params_str);

            $multi_data_key = [];
            $has_spoke = false;
            foreach ($config_params_str_arr as $config_param_str) {
                $config_param_str = trim($config_param_str);
                list($param_name, $param_value) = $this->explodeGule($config_param_str);
                if ($param_name && is_int(strpos($param_value, ':'))) {
                    $multi_data_key[] = $param_name;
                    continue;
                }
                if (is_int(strpos($config_param_str, '{'))) {
                    $has_spoke = true;
                    continue;
                }
                if ($has_spoke && is_int(strpos($config_param_str, '}'))) {
                    $has_spoke = false;
                    continue;
                }
                if (!$has_spoke && $param_name && $param_value) {
                    $param_name = trim($param_name);
                    $param_name = trim($param_name, '\'');
                    $param_value = trim($param_value);
                    $param_value = trim($param_value, '\'');
                    $this->addConfigData($area, $param_name, $param_value);
                }
            }
            foreach ($multi_data_key as $param_name) {
                $start = strpos($require_file_content, $param_name . ':') + strlen($param_name) + 1;
                $after_param_str = trim(substr($require_file_content, $start));
                if (is_int(strpos($after_param_str, '{'))) {
                    $start = strpos($after_param_str, '{') + 1;
                    $end = strpos($after_param_str, '}');
                    $param_json_str = substr($after_param_str, $start, $end - $start);
                    $param_json_arr = explode(',', $param_json_str);
                    foreach ($param_json_arr as $key => $item) {
                        $item = trim($item);
                        list($key_name, $key_value) = $this->explodeGule($item);
                        if ($key_name && $key_value) {
                            $key_value = trim($key_value);
                            if (is_int(strpos($key_value, '['))) {
                                $key_value = trim($key_value, '[');
                                $key_value = trim($key_value, ']');
                                $key_value = trim($key_value);
                                $key_value = trim($key_value, "'");
                                $key_value_arr = explode('/', $key_value);
                                $module = array_shift($key_value_arr);
                                if (strstr($key_value, 'http')) {
                                    $this->config_resources[$area][$param_name][$key_name][] = $key_value;
                                } elseif (in_array($module, array_keys($module_list))) {
                                    $this->config_resources[$area][$param_name][$key_name][] = $this->fetchFile($module . '::' . implode('/', $key_value_arr));
                                } else {
                                    $this->config_resources[$area][$param_name][$key_name][] = $this->fetchFile($require_config_js['module'] . '::' . $key_value);
                                }
                            } else {
                                $key_value = trim($key_value, "'");
                                $key_value_arr = explode('/', $key_value);
                                $module = array_shift($key_value_arr);
                                if (in_array($module, array_keys($module_list))) {
                                    $fetch_value = $this->fetchFile($module . '::' . implode('/', $key_value_arr));
                                    if (isset($this->config_resources[$area][$param_name][$key_name], $_) && $k_data = $this->config_resources[$area][$param_name][$key_name]) {
                                        if (is_array($k_data)) {
                                            $this->config_resources[$area][$param_name][$key_name][] = $fetch_value;
                                        } else {
                                            $this->config_resources[$area][$param_name][$key_name][] = $k_data;
                                            $this->config_resources[$area][$param_name][$key_name][] = $fetch_value;
                                        }
                                    } else {
                                        $this->config_resources[$area][$param_name][$key_name] = $fetch_value;
                                    }
                                } else {
                                    $fetch_value = $this->fetchFile($require_config_js['module'] . '::' . $key_value);
                                    if (isset($this->config_resources[$area][$param_name][$key_name], $_) && $k_data = $this->config_resources[$area][$param_name][$key_name]) {
                                        if (strstr($key_value, 'http')) {
                                            $this->config_resources[$area][$param_name][$key_name][] = $key_value;
                                        }else if (is_array($k_data)) {
                                            $this->config_resources[$area][$param_name][$key_name][] = $fetch_value;
                                        } else {
                                            $this->config_resources[$area][$param_name][$key_name][] = $k_data;
                                            $this->config_resources[$area][$param_name][$key_name][] = $fetch_value;
                                        }
                                    } else {
                                        $this->config_resources[$area][$param_name][$key_name] = $fetch_value;
                                    }
                                }
                            }
                        }
                    }
                }
                if (is_int(strpos($after_param_str, '['))) {
                    $start = strpos($after_param_str, '[') + 1;
                    $end = strpos($after_param_str, ']');
                    $param_json_str = substr($after_param_str, $start, $end - $start);
                    $param_json_arr = explode(',', $param_json_str);
//                    p($param_json_str);
                    foreach ($param_json_arr as $key => $item) {
                        $item = trim($item);
                        list($key_name, $key_value) = $this->explodeGule($item);
                        if ($key_name && $key_value) {
                            $key_value = trim($key_value);
                            if (is_int(strpos($key_value, '['))) {
                                $key_value = trim($key_value, '[');
                                $key_value = trim($key_value, ']');
                                $key_value = trim($key_value);
                                $key_value = trim($key_value, "'");
                                $key_value_arr = explode('/', $key_value);
                                $module = array_shift($key_value_arr);
                                if (strstr($key_value, 'http')) {
                                    $this->config_resources[$area][$param_name][$key_name][] = $key_value;
                                }elseif (in_array($module, array_keys($module_list))) {
                                    $this->config_resources[$area][$param_name][$key_name][] = $this->fetchFile($module . '::' . implode('/', $key_value_arr));
                                } else {
                                    $this->config_resources[$area][$param_name][$key_name][] = $this->fetchFile($require_config_js['module'] . '::' . $key_value);
                                }
                            } else {
                                $key_value = trim($key_value, "'");
                                $key_value_arr = explode('/', $key_value);
                                $module = array_shift($key_value_arr);
                                if (strstr($key_value, 'http')) {
                                    $this->config_resources[$area][$param_name][$key_name] = $key_value;
                                }elseif (in_array($module, array_keys($module_list))) {
                                    $this->config_resources[$area][$key] = $this->fetchFile($module . '::' . implode('/', $key_value_arr));
                                } else {
                                    $this->config_resources[$area][$key] = $this->fetchFile($require_config_js['module'] . '::' . $key_value);
                                }
                            }
                        }
                    }
                }

            }
        }
        p($this->config_resources);
        return $this->config_resources;
    }

    function explodeGule(string $str, string $flag = ':')
    {
        if (is_int(strpos($str, $flag))) {
            $position = strpos($str, $flag);
            return [substr($str, 0, $position), substr($str, $position + 1, strlen($str))];
        } else {
            return false;
        }
    }

    protected Template $template;

    private function getTemplate()
    {
        if (!isset($this->template, $_)) {
            /**@var Template $template */
            $this->template = Template::getInstance()->init();
        }
        return $this->template;
    }

    function fetchFile(string $source)
    {
        return $this->getTemplate()->fetchTemplateTagSourceFile('statics', $source);
    }

    function addConfigData(string $area, string $param_name, string $param_value): array
    {
        if (isset($this->config_resources[$area][$param_name], $_)) {
            if (is_array($this->config_resources[$area][$param_name])) {
                $this->config_resources[$area][$param_name][] = $param_value;
            }
        } else {
            $this->config_resources[$area][$param_name] = $param_value;
        }
        return $this->config_resources;
    }
}