<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console\Resource\Compiler\Statics;

use Weline\Framework\App\Env;
use Weline\Framework\Manager\ObjectManager;
use Weline\Theme\Console\Resource\CompilerInterface;
use Weline\Framework\View\Template;

class Compiler implements CompilerInterface
{

    protected \Weline\Theme\Console\Resource\Compiler\Statics\Reader $reader;
    protected Template $template;
    protected array $config_resources;

    function __construct(
        \Weline\Theme\Console\Resource\Compiler\Statics\Reader $reader
    )
    {
        $this->reader = $reader;
    }

    private function getTemplate()
    {
        if (!isset($this->template, $_)) {
            /**@var Template $template */
            $this->template = Template::getInstance()->init();
        }
        return $this->template;
    }

    public function compile(string $source_file = null, string $out_file = null)
    {
        # require js 配置
        $require_configs = $this->reader->getFileList();
        $module_list = Env::getInstance()->getModuleList();
        foreach ($require_configs as $require_config_js) {
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
                $config_param_str_arr = explode(':', $config_param_str);
                if (2 < count($config_param_str_arr)) {
                    $multi_data_key[] = trim($config_param_str_arr[0]);
                }
                if (is_int(strpos($config_param_str, '{'))) {
                    $has_spoke = true;
                    continue;
                }
                if ($has_spoke && is_int(strpos($config_param_str, '}'))) {
                    $has_spoke = false;
                    continue;
                }
                if (!$has_spoke && 2 === count($config_param_str_arr)) {
                    $param_name = $config_param_str_arr[0];
                    $param_name = trim($param_name);
                    $param_value = $config_param_str_arr[1];
                    $param_value = trim($param_value);
                    $this->addData($area,$param_name, $param_value);
                }
            }
            foreach ($multi_data_key as $param_name) {
                $start = strpos($require_file_content, $param_name . ':') + strlen($param_name)+1;
                $after_param_str = trim(substr($require_file_content, $start));
                if (is_int(strpos($after_param_str, '{'))) {
                    $start = strpos($after_param_str, '{') + 1;
                    $end = strpos($after_param_str, '}');
                    $param_json_str = substr($after_param_str, $start, $end - $start);
                    $param_json_arr = explode(',', $param_json_str);
                    foreach ($param_json_arr as $key => $item) {
                        $item = trim($item);
                        $item_arr = explode(':', $item);
                        $key = $item_arr[0];
                        if (isset($item_arr[1]) && $i_value = $item_arr[1]) {
                            $i_value = trim($i_value);
                            if (is_int(strpos($i_value, '['))) {
                                $i_value = trim($i_value, '[');
                                $i_value = trim($i_value, ']');
                                $i_value = trim($i_value);
                                $i_value = trim($i_value, "'");
                                $i_value_arr = explode('/', $i_value);
                                $module = array_shift($i_value_arr);
                                if (in_array($module, array_keys($module_list))) {
                                    $this->config_resources[$area][$param_name][$key][]= $this->fetchFile($module . '::' . implode('/', $i_value_arr));
                                } else {
                                    $this->config_resources[$area][$param_name][$key] = $this->fetchFile($require_config_js['module'] . '::' . $i_value);
                                }
                            } else {
                                $i_value = trim($i_value, "'");
                                $i_value_arr = explode('/', $i_value);
                                $module = array_shift($i_value_arr);
                                if (in_array($module, array_keys($module_list))) {
                                    $this->config_resources[$area][$param_name][$key][]= $this->fetchFile($module . '::' . implode('/', $i_value_arr));
                                } else {
                                    $this->config_resources[$area][$param_name][$key] = $this->fetchFile($require_config_js['module'] . '::' . $i_value);
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
                    foreach ($param_json_arr as $key => $item) {
                        $item = trim($item);
                        $item_arr = explode(':', $item);
                        $key = $item_arr[0];
                        if (isset($item_arr[1]) && $i_value = $item_arr[1]) {
                            $i_value = trim($i_value);
                            if (is_int(strpos($i_value, '['))) {
                                $i_value = trim($i_value, '[');
                                $i_value = trim($i_value, ']');
                                $i_value = trim($i_value);
                                $i_value = trim($i_value, "'");
                                $i_value_arr = explode('/', $i_value);
                                $module = array_shift($i_value_arr);
                                if (in_array($module, array_keys($module_list))) {
                                    $config_resources[$key][] = $this->fetchFile($module . '::' . implode('/', $i_value_arr));
                                } else {
                                    $config_resources[$key][] = $this->fetchFile($require_config_js['module'] . '::' . $i_value);
                                }
                            } else {
                                $i_value = trim($i_value, "'");
                                $i_value_arr = explode('/', $i_value);
                                $module = array_shift($i_value_arr);
                                if (in_array($module, array_keys($module_list))) {
                                    $config_resources[$key] = $this->fetchFile($module . '::' . implode('/', $i_value_arr));
                                } else {
                                    $config_resources[$key] = $this->fetchFile($require_config_js['module'] . '::' . $i_value);
                                }
                            }
                        }
                    }
                }

            }
        }
        return $this->config_resources;
    }

    function parserRequireJs(){

    }

    function addData(string $area,string $param_name, string $param_value): array
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

    function fetchFile(string $source)
    {
        return $this->getTemplate()->fetchTemplateTagSourceFile('statics', $source);
    }
}