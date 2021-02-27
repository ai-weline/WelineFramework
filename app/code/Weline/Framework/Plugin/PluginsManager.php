<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin;

use Weline\Framework\Plugin\Config\Reader;

class PluginsManager
{
    protected array $plugins = [];
    /**
     * @var Reader
     */
    private Reader $reader;

    public function __construct(
        Reader $reader
    )
    {
        $this->reader = $reader;
    }

    public function scanPlugins()
    {
        if (empty($this->plugins)) {
            // 合并相同类的拦截器
            $plugins = [];
            foreach ($this->reader->read() as $module_and_file => $pluginInstances) {
                foreach ($pluginInstances as $key => $instances) {
                    foreach ($instances as $k => $instance) {
                        if (isset($instance['plugins']['disabled']) && 'true' === $instance['plugins']['disabled']) {
                            unset($instances[$k]);
                        }
                        $plugins[$instance['class']][$instance['plugins']['sort']] = $instance['plugins'];
                    }
                }
                $this->plugins = array_merge($this->plugins, $plugins);
            }
        }
        return $this->plugins;
    }

}
