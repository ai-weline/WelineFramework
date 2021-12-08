<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Manager;

use MongoDB\BSON\Serializable;
use ReflectionClass;
use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\Cache\ObjectCache;
use Weline\Framework\Manager\Cache\ObjectCacheFactory;
use Weline\Framework\View\Template;

class ObjectManager implements ManagerInterface
{
    const unserializable_class = [
        \PDO::class,
        \WeakMap::class
    ];
    private static ?CacheInterface $cache = null;

    private static ?ObjectManager $instance = null;

    private static array $instances=[];

    private function __clone()
    {
    }


    private static function getCache(): CacheInterface
    {
        if (empty(self::$cache)) {
            self::$cache = (new ObjectCache())->create();
        }
        return self::$cache;
    }

    private static function initFactoryClass(string $class): string
    {
        return str_replace('Factory', '', $class);
    }

    /**
     * @DESC         |获取实例(如果不填写class则获取对象管理器本身，如果填写则获取class实例)
     *
     * 参数区：
     *
     * @param string $class
     * @param array $arguments
     * @param bool $shared
     * @return mixed|ObjectManager
     */
    public static function getInstance(string $class = '', array $arguments = [], bool $shared = true): mixed
    {

        if (empty($class)) {
            return self::$instance =new self();
        }
        if (empty(self::$instance)) {
            self::$instance =new self();
        }
        if (isset(self::$instances[$class])) {
            self::$instances[$class] = self::initClassInstance($class, self::$instances[$class]);
            return self::$instances[$class];
        }
        // 缓存对象读取
        if (!CLI && $shared && PROD && $cache_class_object = self::getCache()->get($class)) {
            self::$instances[$class] = self::initClassInstance($class, $cache_class_object);
            return self::$instances[$class];
        }

        // 类名规则处理
        $new_class = self::parserClass($class);
        $arguments = $arguments ?: self::getMethodParams($new_class);
        try {
            $new_object = (new ReflectionClass($new_class))->newInstanceArgs($arguments);
        } catch (\ReflectionException $e) {
            throw $e;
        }
        $new_object = self::initClassInstance($class, $new_object);

        self::addInstance($class, $new_object);
        // 缓存可缓存对象
        if (PROD && !in_array($class, self::unserializable_class)) {
            self::getCache()->set($class, self::$instances[$class]);
        };

        return self::_getInstance($class);
    }

    static function addInstance($class, &$object)
    {
        self::$instances[$class] = $object;
    }

    static function _getInstance($class)
    {
        return self::$instances[$class];
    }

    static function getIs()
    {
        return self::$instances;
    }

    /**
     * 解析类名
     * @param string $class
     * @return string
     */
    public static function parserClass(string $class): string
    {
        // 拦截器处理
        $new_class = $class;
        $interceptor = $class . '\\Interceptor';

        if (class_exists($interceptor)) {
            $new_class = $interceptor;
        }
        // 工厂类处理 工厂类不存在时还原类
        if (!class_exists($class)) {
            $new_class = self::initFactoryClass($class);
        }

        return $new_class;
    }

    /**
     * 初始化类实例
     * @param string $class
     * @param $new_object
     * @return mixed
     */
    private static function initClassInstance(string $class, $new_object): mixed
    {
        $init_method_name = '__init';
        if (method_exists($new_object, $init_method_name)) {
            $new_object->$init_method_name();
        }

        // 工厂类
        if (self::initFactoryClass($class) !== $class) {
            $create_method = 'create';
            if (method_exists($new_object, $create_method)) {
                $new_object = $new_object->$create_method();
            }
        }
        return $new_object;
    }

    /**
     * @Desc         | 创建实例并运行
     * @param $class
     * @param string $method
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     * @throws Exception
     */
    public static function make($class, array $params = [], string $method = '__construct'): mixed
    {
        // 拦截器处理
        $new_class = self::parserClass($class);
        if ('__construct' === $method) {
            $instance = (new ReflectionClass($new_class));
            $method_params = self::getMethodParams($instance, $method);
            foreach ($method_params as $key => $method_param) {
                if (empty($method_param)) {
                    unset($method_params[$key]);
                }
            }
            $method_params = array_merge($method_params, $params);
            $instance = $instance->newInstanceArgs($method_params);
            if (method_exists($instance, '__init')) {
                $instance->__init();
            }
        } else {
            $instance = new ReflectionClass($new_class);
            if (method_exists($instance, '__init')) {
                $instance->__init();
            }
            $paramArr = self::getMethodParams($instance, $method);
            $instance = $instance->{$method}(...array_merge($paramArr, $params));
        }
        return $instance;
    }

    /**
     * @Desc         | 获取方法参数,插件实现
     * @param $className
     * @param string $methodsName
     * @return array
     * @throws Exception
     */
    protected static function getMethodParams($instance_or_class, string $methodsName = '__construct'): array
    {
        // 通过反射获得该类
        if (is_object($instance_or_class)) {
            $className = $instance_or_class::class;
            $class = $instance_or_class;
        } else {
            $className = $instance_or_class;
            try {
                $class = new ReflectionClass($className);
            } catch (\ReflectionException $e) {
                throw new Exception(__('无法实例化该类：%1，错误：%2', [$className, $e->getMessage()]), $e);
            }
        }
        $paramArr = []; // 记录参数，和参数类型（例如：class,string等）

        // 判断该类是否有函数
        if ($class->hasMethod($methodsName)) {
            // 获得构造函数
            try {
                $construct = $class->getMethod($methodsName);
            } catch (\ReflectionException $e) {
                if (CLI or DEV) {
                    echo('无法实例化该类：' . $className . '，错误：' . $e->getMessage());
                }
                throw new Exception(__('无法获得对象方法：%1，错误：%2', [$methodsName, $e->getMessage()]), $e);
            }
            // 判断构造函数是否有参数
            $params = $construct->getParameters();

            if (count($params) > 0) {
                // 判断参数类型
                foreach ($params as $key => $param) {
                    if ($param->getType() && class_exists($param->getType()->getName())) {
                        // 获得参数类型名称
                        $paramTypeName = $param->getType()->getName();
                        if (isset(self::$instances[$paramTypeName])) {
                            $paramArr[] = self::$instances[$paramTypeName];
                        } else {
                            // 获得参数类型
                            $args = self::getMethodParams($paramTypeName);
                            // 实例化时执行自定义__init方法
                            try {
                                $newObj = (new ReflectionClass(self::parserClass($paramTypeName)))->newInstanceArgs($args);
                            } catch (\ReflectionException $e) {
                                if (CLI or DEV) {
                                    echo('无法实例化该类：' . $className . '，错误：' . $e->getMessage());
                                }
                                throw new Exception(__('无法实例化该类：%1，错误：%2', [$paramTypeName, $e->getMessage()]), $e);
                            }
                            if (method_exists($newObj, '__init')) {
                                $newObj->__init();
                            }
                            $paramArr[] = $newObj;
                        }
                    } else {
                        try {
                            if ($param->isDefaultValueAvailable()) {
                                $paramArr[] = $param->getDefaultValue();
                            }
                        } catch (\ReflectionException $e) {
                            if (CLI or DEV) {
                                echo('无法实例化该类：' . $className . '，错误：' . $e->getMessage());
                            }
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
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    protected function getReflectionClass($class): ReflectionClass
    {
        return new \ReflectionClass($class);
    }
}
