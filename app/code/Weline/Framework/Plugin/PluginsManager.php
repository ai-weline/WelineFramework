<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin;

use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Plugin\Api\Data\InterceptorInterface;
use Weline\Framework\Plugin\Cache\PluginCache;
use Weline\Framework\Plugin\Config\Reader;

class PluginsManager
{
    protected array $plugins = [];

    /**
     * @var Reader
     */
    private Reader $reader;

    /**
     * @var CacheInterface
     */
    private CacheInterface $pluginCache;

    public function __construct(
        Reader $reader,
        PluginCache $pluginCache
    )
    {
        $this->reader = $reader;
        $this->pluginCache = $pluginCache->create();
    }

    /**
     * @DESC         |扫描定义的插件
     *
     * 参数区：
     *
     * @param bool $cache
     * @return array
     * @throws \Weline\Framework\App\Exception
     * @throws \Weline\Framework\Exception\Core
     */
    public function scanPlugins(bool $cache=true)
    {
        // 避免重复加载
        if ($this->plugins) {
            return $this->plugins;
        }
        // 检测插件缓存
        if ($cache && $this->plugins = $this->pluginCache->get('plugins_data')) {
            return $this->plugins;
        }
//        p($this->pluginCache->get('plugins_data'));

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
        $plugins_info = [];
        // 检查定义所有插件类的方法（方法列表要求：
        //1、 读取所有插件的方法的名字必须在被侦听的类中的方法中存在
        //2、 全局原始类函数，用于创建侦听类使用
        //）

        // 反射所有插件类方法
        foreach ($plugins as $type => $type_plugins) {
            $plugin_listen_methods = [];
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
            foreach ($type_methods as $key => $method) {
                unset($type_methods[$key]);
                if ($type === $method->class) {
                    $type_methods[$method->name] = $method->name;
                }
            }
            $plugins_info[$type]['methods'] = $type_methods;

            foreach ($type_plugins as $type_plugin) {
                try {
                    $typePluginRef = new \ReflectionClass($type_plugin['instance']);
                } catch (\ReflectionException $e) {
                    throw new \Error($e->getMessage(), $e->getCode(), $e);
                }
                if ($typePluginRef->isFinal()) {
                    throw new \Error(__('插件名称：%name,' . PHP_EOL . '无法动态代理final类:%instance' . PHP_EOL . '状态：%disabled' . PHP_EOL . '排序：%sort', $type_plugin));
                }
                $plugin_instance_methods = $typePluginRef->getMethods();
                foreach ($plugin_instance_methods as $key => $plugin_instance_method) {
                    unset($plugin_instance_methods[$key]);
                    // 获取当前类的方法
                    if (trim($type_plugin['instance'], '\\') === $plugin_instance_method->class) {
                        $name = str_replace(
                            [
                                InterceptorInterface::LISTENER_BEFORE,
                                InterceptorInterface::LISTENER_AROUND,
                                InterceptorInterface::LISTENER_AFTER,
                            ],
                            '',
                            $plugin_instance_method->name
                        );

                        // 检测首字母大小写字母匹配的方法是否存在：存在则不新增
                        if (!in_array($name, $plugin_instance_methods, true) || !in_array(lcfirst($name), $plugin_instance_methods, true)) {
                            // 检测首字母大小写字母匹配的方法
                            if (in_array($name, $type_methods, true)) {
                                $plugin_instance_methods[$name][] = $plugin_instance_method->name;
                            }
                            if (in_array(lcfirst($name), $type_methods, true)) {
                                $plugin_instance_methods[lcfirst($name)][] = $plugin_instance_method->name;
                            }
                        }

                        // 检测首字母大小写字母匹配的方法是否存在：存在则不新增:全局原始类函数，用于创建侦听类使用
                        $origin_in_listen = in_array($name, $type_methods, true);
                        if (!in_array($name, $plugin_listen_methods, true) && $origin_in_listen) {
                            // 检测首字母大小写字母匹配的方法
                            $origin_is_in = false;
                            if (!in_array($name, $plugin_listen_methods, true)) {
                                $plugin_listen_methods[] = $name;
                                $origin_is_in = true;
                            }
                            // 如果不在继续查
                            if (!$origin_is_in) {
                                if (!in_array($name, $plugin_listen_methods, true)) {
                                    $plugin_listen_methods[] = lcfirst($name);
                                    $origin_is_in = true;
                                }
                            }
                            // 还不在就创建
                            if (!$origin_is_in) {
                                $plugin_listen_methods[] = lcfirst($name);
                            }
                        }

                        // 检测首字母大小写字母匹配的方法是否存在：存在则不新增:全局原始类函数，用于创建侦听类使用
                        $lcfirst_name = lcfirst($name);
                        $lcfirst_in_listen = in_array($lcfirst_name, $type_methods, true);
                        if (!in_array($lcfirst_name, $plugin_listen_methods, true) && $lcfirst_in_listen) {
                            // 检测首字母大小写字母匹配的方法
                            $lcfirst_is_in = false;
                            if (!in_array($lcfirst_name, $plugin_listen_methods, true)) {
                                $plugin_listen_methods[] = $lcfirst_name;
                                $lcfirst_is_in = true;
                            }
                            // 如果不在继续查
                            if (!$lcfirst_is_in) {
                                if (!in_array($lcfirst_name, $plugin_listen_methods, true)) {
                                    $plugin_listen_methods[] = $lcfirst_name;
                                    $lcfirst_is_in = true;
                                }
                            }
                            // 还不在就创建
                            if (!$lcfirst_is_in) {
                                $plugin_listen_methods[] = $lcfirst_name;
                            }
                        }
                    }
                }
                $plugins_info[$type]['plugins_methods'][$type_plugin['instance']] = $plugin_instance_methods;
            }
            $plugins_info[$type]['listen_methods'] = $plugin_listen_methods;
        }

        // 正式环境则缓存
        if (!DEV) {
            $this->plugins = $this->pluginCache->set('plugins_data', $plugins_info);
        }
        $this->pluginCache->set('plugins_data', $plugins_info);
        $this->plugins = $this->pluginCache->get('plugins_data');
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
            $plugin_instance_list = $this->scanPlugins()[$type];
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
     * @param bool $cache
     * @return Proxy\Generator
     * @throws \ReflectionException
     * @throws \Weline\Framework\App\Exception
     * @throws \Weline\Framework\Exception\Core
     */
    public function generatorInterceptor(string $class = '', bool $cache = true)
    {
        /**@var \Weline\Framework\Plugin\Proxy\Generator $generator */
        $generator = ObjectManager::getInstance(\Weline\Framework\Plugin\Proxy\Generator::class);
        if ($class) {
            $generator::createInterceptor($class);
        } else {
            foreach ($this->scanPlugins($cache) as $origin_class => $scanPlugin) {
                $generator::createInterceptor($origin_class);
            }
        }
        return $generator;
    }
}
