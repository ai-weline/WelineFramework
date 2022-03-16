<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Manager;

use ReflectionClass;
use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Manager\Cache\ObjectCache;

class ObjectManager implements ManagerInterface
{
    const unserializable_class = [
        \PDO::class,
        \WeakMap::class
    ];
    private static ?CacheInterface $cache = null;

    private static ?ObjectManager $instance = null;

    private static array $instances = [];

    private function __clone()
    {
    }

    function __init()
    {
        self::getCache();
    }
    function __construct(){
        self::getCache();
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
        if (!class_exists($class)) {
            if (str_ends_with($class, 'Factory')) {
                $class = substr($class, 0, strrpos($class, 'Factory'));
            }
            if (!class_exists($class)) throw new Exception(__("工厂类：{$class} 不存在！"));
        }
        return $class;
    }

    /**
     * @DESC         |获取实例(如果不填写class则获取对象管理器本身，如果填写则获取class实例)
     *
     * 参数区：
     *
     * @param string $class
     * @param array  $arguments
     * @param bool   $shared
     *
     * @return mixed
     */
    public static function getInstance(string $class = '', array $arguments = [], bool $shared = true, bool $cache = false): mixed
    {
        if (empty($class)) {
            return self::$instance = new self();
        }
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        if (isset(self::$instances[$class])&&$obj =self::$instances[$class]) {
            return $obj;
        }
        // 缓存对象读取
        if ($cache && !CLI && $shared && PROD && $cache_class_object = self::getCache()->get($class)) {
            self::$instances[$class] = self::initClassInstance($class, $cache_class_object);
            return self::$instances[$class];
        }
        // 类名规则处理
        $class_cache_key = $class . '_cache_key';
        $new_class       = self::$cache->get($class_cache_key);
        if (empty($new_class)) {
            $new_class = self::parserClass($class);
            self::$cache->set($class_cache_key, $new_class);
        }
        $arguments = $arguments ?: self::getMethodParams($new_class);
//        if ($new_class == 'Aiweline\Bbs\Controller\Index') {
//            p($arguments);
//        }
        $refClass = (new \ReflectionClass($new_class));
//        p($refClass->getAttributes());
        $new_object = $refClass->newInstanceArgs($arguments);
        /*$classAttrs = $refClass->getAttributes();
        foreach ($classAttrs as $key => $classAttr) {
            $value = $classAttr->getName();
            if ($value === $scanAnno) {
                $refProperties =  $refClass->getProperties();
                $obj = $refClass->newInstance();
                foreach ($refProperties as $key => $refProperty) {
                    $refProperty = $refClass->getProperty($refProperty->getName());
                    $propertyAttrs = $refProperty->getAttributes();
                    $value = $propertyAttrs[0]->getArguments();
                    $refProperty->setValue($obj, $value[0]);
                    $container[$class] = $obj;
                }
            }
        }*/
        $new_object = self::initClassInstance($class, $new_object);

        self::addInstance($class, $new_object);
        // 缓存可缓存对象
        if ($cache && !CLI && PROD && !in_array($class, self::unserializable_class)) {
            self::getCache()->set($class, self::$instances[$class]);
        };

        return self::_getInstance($class);
    }

    /**
     * @DESC          # 设置实例
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/1/5 22:49
     * 参数区：
     *
     * @param string $class
     * @param object $object
     *
     * @return mixed
     */
    public static function setInstance(string $class, object &$object): mixed
    {
        self::$instances[$class] = $object;
        return true;
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
     *
     * @param string $class
     *
     * @return string
     */
    public static function parserClass(string $class): string
    {
        // 拦截器处理
        $interceptor = $class . '\\Interceptor';

        if (class_exists($interceptor)) {
            return $interceptor;
        }
        // 工厂类处理 工厂类不存在时还原类
        return self::initFactoryClass($class);
    }

    /**
     * 初始化类实例
     *
     * @param string $class
     * @param        $new_object
     *
     * @return mixed
     */
    private static function initClassInstance(string $class, $new_object): mixed
    {
        $init_method_name = '__init';
        if (method_exists($new_object, $init_method_name)) {
            $new_object->$init_method_name();
        }
        // 工厂类
        if (str_ends_with($class, 'Factory')) {
            $create_method = 'create';
            if (method_exists($new_object, $create_method)) {
                $new_object = $new_object->$create_method();
            }
        }
        return $new_object;
    }

    /**
     * @Desc         | 创建实例并运行
     * @param        $class
     * @param string $method
     * @param array  $params
     *
     * @return mixed
     * @throws \ReflectionException
     * @throws Exception
     */
    public static function make($class, array $params = [], string $method = '__construct'): mixed
    {
        // 拦截器处理
        $new_class = self::parserClass($class);
        if ('__construct' === $method) {
            $instance      = (new ReflectionClass($new_class));
            $method_params = self::getMethodParams($instance, $method);
            foreach ($method_params as $key => $method_param) {
                if (empty($method_param)) {
                    unset($method_params[$key]);
                }
            }
            $method_params = array_merge($method_params, $params);
            $instance      = $instance->newInstanceArgs($method_params);
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
     * @param        $className
     * @param string $methodsName
     *
     * @return array
     * @throws Exception
     */
    protected static function getMethodParams($instance_or_class, string $methodsName = '__construct'): array
    {
        // 通过反射获得该类
        if (is_object($instance_or_class)) {
            $className = $instance_or_class::class;
            $class     = $instance_or_class;
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
            // TODO 完成自动注入在 PHP 8.1环境下的问题
            $params = $construct->getParameters();
            if (count($params) > 0) {
                // 判断参数类型
                foreach ($params as $key => $param) {
//                    if($instance_or_class=='Aiweline\Bbs\Controller\Index'){
//                        p($param->getType()->getName());
//                    }
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
     *
     * @return ReflectionClass
     * @throws \ReflectionException
     */
    protected function getReflectionClass($class): ReflectionClass
    {
        return new \ReflectionClass($class);
    }
}
