<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin;

use Weline\Framework\Manager\ObjectManager;
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
    ) {
        $this->reader = $reader;
    }

    /**
     * @DESC         |扫描定义的插件
     *
     * 参数区：
     *
     * @return array
     * @throws \Weline\Framework\Exception\Core
     */
    protected function scanPlugins()
    {
        if($this->plugins){
            return $this->plugins;
        }

        $plugins = [];
        if (empty($this->plugins)) {
            // 合并相同类的拦截器
            foreach ($this->reader->read() as $module_and_file => $pluginInstances) {
                foreach ($pluginInstances as $key => $instances) {
                    foreach ($instances as $k => $instance) {
                        if (isset($instance['plugins']['disabled']) && 'true' === $instance['plugins']['disabled']) {
                            unset($instances[$k]);
                        }
                        $plugins[$instance['class']][$instance['plugins']['sort']] = $instance['plugins'];
                    }
                }
            }
        }

        // 反射所有插件类方法
        foreach ($plugins as $type=>$type_plugins) {
            try {
                $typeRef = new \ReflectionClass($type);
            } catch (\ReflectionException $e) {
                throw new \Error($e->getMessage(), $e->getCode(), $e);
            }
            if ($typeRef->isFinal()) {
                throw new \Error(__('无法动态代理final类:%1', [$type]));
            }
            // 读取被侦听拦截原始类的方法列表
            $type_methods = $typeRef->getMethods();
            foreach ($type_methods as $key=>$method) {
                if($type!==$method->class){
                    unset($type_methods[$key]);
                }
            }
            // 检查定义所有插件类的方法（方法列表要求：
            //1、读取所有插件的方法的名字必须在被侦听的类中的方法中存在
            //）
            $plugins_methods = [];
            foreach ($type_plugins as $type_plugin) {
                try {
                    $typePluginRef = new \ReflectionClass($type_plugin['instance']);
                } catch (\ReflectionException $e) {
                    throw new \Error($e->getMessage(), $e->getCode(), $e);
                }
                if ($typePluginRef->isFinal()) {
                    throw new \Error(__('插件名称：%name,'.PHP_EOL.'无法动态代理final类:%instance'.PHP_EOL.'状态：%disabled'.PHP_EOL.'排序：%sort', $type_plugin));
                }
                $plugin_instance_methods = $typePluginRef->getMethods();
                foreach ($plugin_instance_methods as $key=>$plugin_instance_method) {
                    if($type_plugin['instance']!==$plugin_instance_method->class){
                        unset($plugin_instance_methods[$key]);
                    }
                }
                p($plugin_instance_methods);
            }

        }
        return $this->plugins;
    }

    /**
     * @DESC         |获取类的插件类列表
     *
     * 参数区：
     *
     * @param string $class
     * @return array|mixed
     */
    protected function getClassPluginInstanceList(string $class = '')
    {
        $plugins = $this->scanPlugins();
        if ($class) {
            return $plugins[$class] ?? [];
        }

        return $plugins;
    }

    /**
     * @DESC         |获取插件信息
     * 读取插件所有信息：插件定义的所有方法的 前置、环绕、后置 拦截信息
     *
     * 参数区：
     * @param string $type
     * @param string $method
     * @param string|null $code
     */
    public function getPluginInfo(string $type, string $method, string $code = null)
    {
        $plugin_instance_list = [];
        if (isset($this->scanPlugins()[$type])) {
            $plugin_instance_list =  $this->scanPlugins()[$type];
        }
        if (empty($plugin_instance_list)) {
            # 浪费空间去创建空数组，不如直接返回已有的空数组
            return $plugin_instance_list;
        }

        return [];
    }

    /**
     * @DESC         |为插件创建 侦听 类
     *
     * 参数区：
     *
     * @param string $class
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     */
    public function generatorInterceptor(string $class = '')
    {
        /**@var \Weline\Framework\Plugin\Proxy\Generator $generator */
        $generator = ObjectManager::getInstance(\Weline\Framework\Plugin\Proxy\Generator::class);
        if ($class) {
            $generator::createInterceptor($class);
        } else {
            foreach ($this->scanPlugins() as $origin_class => $scanPlugin) {
                $generator::createInterceptor($origin_class);
            }
        }
    }
}
