<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/6
 * 时间：17:44
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Manager;


abstract class AbstractManager
{
    private static ObjectManager $instance;
    protected array $objects = array();

    private function __clone()
    {
    }

    /**
     * @DESC         |获取实例
     *
     * 参数区：
     *
     * @return ObjectManager
     */
    static function getInstance()
    {
        if (!isset(self::$instance)) self::$instance = new ObjectManager();
        return self::$instance;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $class
     * @return object
     */
    function create(string $class): object
    {
        if (isset($this->objects[$class])) return $this->objects[$class];
        $this->objects[$class] = new $class();
        return $this->objects[$class];
    }
}