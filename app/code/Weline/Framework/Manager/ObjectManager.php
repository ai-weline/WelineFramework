<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Manager;

use ReflectionClass;
use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Manager\Cache\ObjectCache;

class ObjectManager implements ManagerInterface
{
    private static CacheInterface $cache;

    private static ObjectManager $instance;

    private static array $instances;

    private static string $current_class;

    private function __clone()
    {
    }

    private static function getCache()
    {
        if (! isset(self::$cache)) {
            self::$cache = (new ObjectCache())->create();
        }

        return self::$cache;
    }

    public static function getClass()
    {
        return self::$current_class;
    }

    private static function setClass(string $class)
    {
        self::$current_class = $class;
    }

    private static function initSelf()
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }
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
        self::initSelf();
        self::setClass($class);

        // 缓存对象读取
        if (! DEV && $class_object = self::getCache()->get($class)) {
            self::$instances[$class] = self::initClass($class_object);

            return self::$instances[$class];
        }
        if (empty($class)) {
            return isset(self::$instance) ? self::$instance : new self();
        }
        if (isset(self::$instances[$class])) {
            return self::$instances[$class];
        }

        // 拦截器处理
        $new_class = self::parserClass($class);

        $paramArr = self::getMethodParams($new_class);

        $new_object = (new ReflectionClass($new_class))->newInstanceArgs($paramArr);

        self::$instances[$class] = self::initClass($new_object);
        // 缓存对象
        self::getCache()->set($class, self::$instances[$class]);

        return self::$instances[$class];
    }

    public static function parserClass(string $class)
    {
        // 拦截器处理
        $new_class       = $class;
        $interceptor     = $class . '\\Interceptor';
        $interceptorFile = Env::path_framework_generated_code . str_replace('\\', DIRECTORY_SEPARATOR, $interceptor) . '.php';

        if (is_file($interceptorFile)) {
            $new_class = $interceptor;
        }

        return $new_class;
    }

    private static function initClass($new_object)
    {
        if (method_exists($new_object, '__init')) {
            $new_object->__init();
        }

        return $new_object;
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
        // 拦截器处理
        $new_class = self::parserClass($className);

        if ('__construct' === $methodName) {
            // 如果是初始化函数则返回一个初始化后的对象
            $instance                    = (new ReflectionClass($new_class))->newInstanceArgs($params);
            self::$instances[$className] = $instance;

            return self::$instances[$className];
        }
        // 如果不是则实例化后立即执行该函数
        // 获取该方法所需要依赖注入的参数
        $paramArr = self::getMethodParams($className, $methodName);
        // 获取类的实例
        self::$instances[$className] = self::getInstance($className);

        return self::$instances[$className]->{$methodName}(...array_merge($paramArr, $params));
    }

    /**
     * @Desc         | 获取方法参数,插件实现
     * @param $className
     * @param string $methodsName
     * @throws Exception
     * @return array
     */
    protected static function getMethodParams($className, $methodsName = '__construct')
    {
        // 通过反射获得该类
        try {
            $class = new ReflectionClass($className);
        } catch (\ReflectionException $e) {
            throw new Exception(__('无法实例化该类：%1，错误：%2', [$className, $e->getMessage()]), $e);
        }

        $paramArr = []; // 记录参数，和参数类型（例如：class,string等）

        // 判断该类是否有函数
        if ($class->hasMethod($methodsName)) {
            // 获得构造函数
            try {
                $construct = $class->getMethod($methodsName);
            } catch (\ReflectionException $e) {
                throw new Exception(__('无法获得对象方法：%1，错误：%2', [$methodsName, $e->getMessage()]), $e);
            }
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
                            try {
                                $newObj = (new ReflectionClass(self::parserClass($paramClass->getName())))->newInstanceArgs($args);
                            } catch (\ReflectionException $e) {
                                throw new Exception(__('无法实例化该类：%1，错误：%2', [$paramClass->getName(), $e->getMessage()]), $e);
                            }
                            if (method_exists($newObj, '__init')) {
                                $newObj->__init();
                            }
                            $paramArr[] = $newObj;
                        }
                    } else {
                        try {
                            $paramArr[] = $param->getDefaultValue();
                        } catch (\ReflectionException $e) {
                            throw new Exception(__('错误的参数：%1', [$e->getMessage()]), $e);
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
