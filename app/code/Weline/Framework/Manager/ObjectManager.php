<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Manager;

use ReflectionClass;

class ObjectManager implements ManagerInterface
{
    private static ObjectManager $instance;

    private static array $instances;

    private function __clone()
    {
    }

    private function __construct()
    {
    }

    /**
     * @DESC         |获取实例(如果不填写class则获取对象管理器本身，如果填写则获取class实例)
     *
     * 参数区：
     *
     * @param string $class
     * @throws \ReflectionException
     * @return mixed|ObjectManager
     */
    public static function getInstance(string $class = '')
    {
        if (empty($class)) {
            return isset(self::$instance) ? self::$instance : new self();
        }
        if (isset(self::$instances[$class])) {
            return self::$instances[$class];
        }
        $paramArr = self::getMethodParams($class);
        // TODO 检查插件（插件的方法），然后动态为实例化后的对象添加动态函数（某方法之前，之后，环绕等）
        $new_object = (new ReflectionClass($class))->newInstanceArgs($paramArr);
        // 自定义初始化函数
        if (method_exists($new_object, '__init')) {
            $new_object->__init();
        }
        self::$instances[$class] = $new_object;

        return self::$instances[$class];
    }

    /**
     * @Desc         | 创建实例并运行
     * @param $className
     * @param string $methodName
     * @param array $params
     * @throws \ReflectionException
     * @return mixed
     */
    public static function make($className, $methodName = '__construct', $params = [])
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
     */
    protected static function getMethodParams($className, $methodsName = '__construct')
    {
        // 通过反射获得该类
        $class = new ReflectionClass($className);

        $paramArr = []; // 记录参数，和参数类型（例如：class,string等）

        // 判断该类是否有函数
        if ($class->hasMethod($methodsName)) {
            // 获得构造函数
            $construct = $class->getMethod($methodsName);
            // 判断构造函数是否有参数
            $params = $construct->getParameters();

            if (count($params) > 0) {
                // 判断参数类型
                foreach ($params as $key => $param) {
                    if ($paramClass = $param->getClass()) {
                        if (isset(self::$instances[$paramClass->getName()])) {
                            $paramArr[] = self::$instances[$paramClass->getName()];
                        } else {
                            // 获得参数类型名称
                            $paramClassName = $paramClass->getName();
                            // 获得参数类型
                            $args = self::getMethodParams($paramClassName);
                            // 实例化时执行自定义__init方法
                            $newObj = (new ReflectionClass($paramClass->getName()))->newInstanceArgs($args);
                            if (method_exists($newObj, '__init')) {
                                $newObj->__init();
                            }
                            $paramArr[] = $newObj;
                        }
                    }
                }
            }
        }

        return $paramArr;
    }

    /**
     * @DESC         |读取类反射
     *
     * 参数区：
     *
     * @param $class
     * @throws \ReflectionException
     * @return ReflectionClass
     */
    protected function getReflectionClass($class): ReflectionClass
    {
        return new \ReflectionClass($class);
    }
}
