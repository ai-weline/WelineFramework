<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/26
 * 时间：12:34
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Manager;

use ReflectionClass;

class ObjectManager
{
    private static ObjectManager $instance;
    private static array $instances;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    private function __construct()
    {
    }

    /**
     * @Desc         | 获取实例(如果不填写class则获取对象管理器本身，如果填写则获取class实例)
     * @param string $class
     * @return mixed|ObjectManager
     */
    public static function getInstance(string $class = '')
    {
        if (empty($class)) return isset(self::$instance) ?self::$instance:new self();
        if (isset(self::$instances[$class])) return self::$instances[$class];
        $paramArr = self::getMethodParams($class);
        self::$instances[$class] = (new ReflectionClass($class))->newInstanceArgs($paramArr);
        return self::$instances[$class];
    }


    /**
     * @Desc         | 创建实例 用于新类实例化
     * @param $className
     * @param string $methodName
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     */
    public static function create($className, $methodName = '__construct', $params = [])
    {
        // 获取类的实例
        $instance = self::getInstance($className);

        // 获取该方法所需要依赖注入的参数
        $paramArr = self::getMethodParams($className, $methodName);

        return $instance->{$methodName}(...array_merge($paramArr, $params));
    }


    /**
     * @Desc         | 获取方法参数,插件实现
     * @param $className
     * @param string $methodsName
     * @return array
     * @throws \ReflectionException
     */
    protected static function getMethodParams($className, $methodsName = '__construct')
    {
        // 通过反射获得该类
        $class = new ReflectionClass($className);

        $paramArr = []; // 记录参数，和参数类型（例如：class,string等）

        // 判断该类是否有构造函数
        if ($class->hasMethod($methodsName)) {
            // 获得构造函数
            $construct = $class->getMethod($methodsName);

            // 判断构造函数是否有参数
            $params = $construct->getParameters();

            if (count($params) > 0) {
                // 判断参数类型
                foreach ($params as $key => $param) {
                    if ($paramClass = $param->getClass()) {
                        // 获得参数类型名称
                        $paramClassName = $paramClass->getName();
                        // 获得参数类型
                        $args = self::getMethodParams($paramClassName);
                        $paramArr[] = (new ReflectionClass($paramClass->getName()))->newInstanceArgs($args);
                    }
                }
            }
        }
        return $paramArr;
    }
}