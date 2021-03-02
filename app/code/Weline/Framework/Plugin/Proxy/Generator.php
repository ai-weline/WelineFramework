<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin\Proxy;

use Weline\Framework\App\Env;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Plugin\PluginsManager;

class Generator
{
    private static string $proxyClassTemplate = '<?php
/**
 * 文件信息 Weline框架自动侦听拦截类，请勿随意修改，以免造成系统异常
 * 作者：WelineFramework                       【Aiweline/邹万才】
 * 网名：WelineFramework框架                    【秋风雁飞(Aiweline)】
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：WelineFramework框架
 * 日期：${DATE}
 * 时间：${TIME}
 * 描述：此文件源码由WelineFramework框架自动侦听拦截类，请勿随意修改源码，以免造成系统异常！
 */
namespace ${namespace};

class ${className} extends ${targetClass}
{
    // 继承侦听器trait
    use \Weline\Framework\Interception\Interceptor;
${functionList}
}
';

    // 代理类关系:多次加载时减少重复
    private static array $classProxyMap = [];

    public static function createInterceptor(string $class)
    {
        return self::getProxyInterceptor($class);
    }

    private static function getProxyInterceptor(string $targetClassName): array
    {
        $proxyClassName = self::$classProxyMap[$targetClassName] ?? null;
        if ($proxyClassName !== null) {
            return $proxyClassName;
        }

        $proxyClass = self::genProxyClass($targetClassName);

        //eval();动态加载代码

        self::$classProxyMap[$targetClassName] = $proxyClass;

        return $proxyClass;
    }

    private static function genProxyClass(string $class)
    {
        try {
            $classRef = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            throw new \Error($e->getMessage(), $e->getCode(), $e);
        }
        if ($classRef->isFinal()) {
            throw new \Error(__('无法动态代理final类:%1', [$class]));
        }
        $functionList = [];
        $methods      = $classRef->getMethods(\ReflectionMethod::IS_PUBLIC);
        // 仅监听被监听的函数
        /**@var PluginsManager $pluginsManager*/
        $pluginsManager = ObjectManager::getInstance(PluginsManager::class);
        $type_plugin = $pluginsManager->getClassPluginInstanceList($class);
        $plugin_listen_type_methods = isset($type_plugin['listen_methods'])?$type_plugin['listen_methods']:[];
        $plugin_listen_type_methods[] = '__construct';
        // 排除当前类尚未代理的函数
        foreach ($methods as $key => $method) {
            if ($class !== $method->class||!in_array($method->name,$plugin_listen_type_methods)) {
                unset($methods[$key]);
            }
        }

        // 创建侦听代理函数
        foreach ($methods as $method) {
            if ($method->isFinal()) {
                throw new \Error('无法动态代理final方法' . $method->name);
            }
            // 获取返回类型签名
            $methodReturnType = $method->getReturnType();
            if ($methodReturnType === null) {
                $methodReturnType = '';
            } else {
                $methodReturnType = ': \\' . $methodReturnType->getName();
            }
            // 方法参数
            $args       = [];
            $parameters = [];

            foreach ($method->getParameters() as $parameter) {
                // 处理默认值
                $parameter_value = null;

                try {
                    $parameter_value = $parameter->getDefaultValue();
                    $parameter_value = '=' . var_export($parameter_value, true);
                } catch (\Exception $exception) {
                    $parameter_value = '';
                }
                $parameter_type = $parameter->hasType() ? '\\' . $parameter->getType()->getName() : '';
                $args[]         = $parameter_type . ' $' . $parameter->getName() . $parameter_value;
                $parameters[]   = '$' . $parameter->getName();
            }
            $args_tpl   = implode(',' . PHP_EOL . '        ', $args);
            $params_tpl = implode(',' . PHP_EOL . '        ', $parameters);

            // 方法模板
            $func_tpl = '
    ${func_doc}
    public function ${methodName}(
        ${arguments}
    )${returntype}
    {
        ${construct_content}
        $pluginInfo = $this->pluginsManager->getPluginInfo($this->subjectType, \'${methodName}\');
        if (!$pluginInfo) {
            return parent::${methodName}(${parameters});
        } else {
            return $this->___callPlugins(\'${methodName}\', func_get_args(), $pluginInfo);
        } 
    }';
            $construct_func_tpl = '
    ${func_doc}
    public function ${methodName}(
        ${arguments}
    )${returntype}
    {
        ${construct_content}
    }';
            $construct_content = '';
            if ('__construct' === $method->name) {
                $construct_content = '
        $this->___init();
        parent::__construct(' . $params_tpl . ');
                    ';
                $func_tpl = $construct_func_tpl;
            }
            $functionList[] = '    ' . str_replace(
                [
                    '${methodName}',
                    '${returntype}',
                    '${arguments}',
                    '${parameters}',
                    '${func_doc}',
                    '${construct_content}',
                ],
                [
                    $method->name,
                    $methodReturnType,
                    $args_tpl,
                    $params_tpl,
                    $method->getDocComment(),
                    $construct_content,
                ],
                $func_tpl
            );
        }
        $replaceMap = [
            '${DATE}'      => date('Y-m-d'),
            '${TIME}'      => date('H:m:s'),
            '${namespace}' => $class,
            '${className}' => /*$classRef->getShortName().*/
                'Interceptor',
            '${targetClass}'  => '\\' . $class,
            '${functionList}' => join(PHP_EOL, $functionList),
        ];
        $classBody = str_replace(array_keys($replaceMap), array_values($replaceMap), self::$proxyClassTemplate);

        // 写入代理文件
        $class_name       = $replaceMap['${namespace}'] . '\\' . $replaceMap['${className}'];
        $interceptor_path = Env::path_framework_generated_code . $class_name . '.php';

        /**@var \Weline\Framework\System\File\Io\File $file */
        $file = ObjectManager::getInstance(\Weline\Framework\System\File\Io\File::class);
        $file->open($interceptor_path, \Weline\Framework\System\File\Io\File::mode_w)
            ->write($classBody)
            ->close();

        return [
            'name' => '\\' . $class_name,
            'body' => $classBody,
            'file' => $interceptor_path,
        ];
    }

    /**
     * @return array
     */
    public static function getClassProxyMap(): array
    {
        return self::$classProxyMap;
    }

    /**
     * 设置
     * @param array $classProxyMap
     */
    public static function setClassProxyMap(array $classProxyMap): void
    {
        self::$classProxyMap = $classProxyMap;
    }
}
