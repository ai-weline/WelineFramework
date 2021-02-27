<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/2/2
 * 时间：22:03
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Plugin\Proxy;


use Weline\Framework\App\Env;

class Generator
{
    private static string $proxyClassTemplate = 'namespace ${namespace};
${use_list}
class ${className} extends ${targetClass}{
private $cglibProxyTargetObj;
public function __construct($target){ $this->cglibProxyTargetObj = $target; }
${functionList}
}
';

    // 代理类关系
    private static array $classProxyMap = [];


    public static function getProxy(object $obj, array $plugins)
    {
        // TODO 完善所有代理类的方法形成方法列表并排序，并生成对应代理类
        $className = '\\' . get_class($obj);

        $proxyClassName = self::getProxyClassName($className);
//        p($proxyClassName);

        return new $proxyClassName($obj);
    }

    private static function getProxyClassName(string $targetClassName): string
    {
        $proxyClassName = self::$classProxyMap[$targetClassName] ?? null;
        if ($proxyClassName !== null) {
            return $proxyClassName;
        }

        $proxyClass = self::genProxyClass($targetClassName);

        p($proxyClass['body']);
        // 加载类
        eval($proxyClass['body']);

        $proxyClassName = $proxyClass['name'];
        self::$classProxyMap[$targetClassName] = $proxyClassName;

        return $proxyClassName;
    }


    private static function genProxyClass(string $class)
    {
        try {
            $classRef = new \ReflectionClass($class);
        } catch (\ReflectionException $e) {
            throw new \Error($e->getMessage(), $e->getCode(), $e);
        }
        if ($classRef->isFinal()) {
            throw new \Error('无法动态代理final类' . $class);
        }

        $funcTpl = 'public function ${methodName}(...$args)${returntype}{ return AOP::invoke(new AspectPoint($this->cglibProxyTargetObj, \'${methodName}\', $args)); }';
        $functionList = [];
        $methods = $classRef->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->isFinal()) {
                throw new \Error('无法动态代理final方法' . $method->name);
            }
            // 获取返回类型签名
            $methodReturnType = $method->getReturnType();
            if ($methodReturnType === null) {
                $methodReturnType = '';
            } else {
                $methodReturnType = ': ' . $methodReturnType;
            }
            $functionList[] = str_replace(['${methodName}', '${returntype}'], [$method->name, $methodReturnType], $funcTpl);
        }
        p($functionList);
//        $replaceMap = [
//            '${namespace}' => $class. '\\CGProxy',
//            '${className}' => $classRef->getShortName() . '_CGProxy',
//            '${targetClass}' => $class,
//            '${functionList}' => join(PHP_EOL, $functionList),
//        ];
        $replaceMap = [
            '${namespace}' => /*'Interceptor' . */$class . '\\Interceptor',
            '${use_list}' => $class,
            '${className}' => /*$classRef->getShortName().*/'Interceptor',
            '${targetClass}' => $class,
            '${functionList}' => join(PHP_EOL, $functionList),
        ];
        $classBody = str_replace(array_keys($replaceMap), array_values($replaceMap), self::$proxyClassTemplate);

        return [
            'name' => '\\' . $replaceMap['${namespace}'] . '\\' . $replaceMap['${className}'],
            'body' => $classBody,
        ];
    }
}