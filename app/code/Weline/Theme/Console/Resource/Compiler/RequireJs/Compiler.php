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
use Weline\Framework\Manager\ObjectManager;
use Weline\Theme\Console\Resource\CompilerInterface;
use Weline\Framework\View\Template;

class Compiler implements CompilerInterface
{

    protected \Weline\Theme\Console\Resource\Compiler\RequireJs\Reader $reader;
    protected Template $template;

    function __construct(
        \Weline\Theme\Console\Resource\Compiler\RequireJs\Reader $reader
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
        $config_resources = $this->reader->parserRequireConfigs();
        foreach ($config_resources as $config_resource) {
            p($config_resource);
        }
        return ;
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