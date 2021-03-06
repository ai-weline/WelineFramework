<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Interception;

use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Plugin\Api\Data\InterceptorInterface;
use Weline\Framework\Plugin\PluginsManager;

trait Interceptor
{
    private string $subjectType;

    private PluginsManager $pluginsManager;

    public function ___init()
    {
        $this->pluginsManager = ObjectManager::getInstance(PluginsManager::class);
        $this->subjectType    = get_parent_class($this);
    }

    /**
     * @return string
     */
    public function getSubjectType(): string
    {
        return $this->subjectType;
    }

    /**
     * 设置
     * @param string $subjectType
     */
    public function setSubjectType(string $subjectType): void
    {
        $this->subjectType = $subjectType;
    }

    /**
     * @return PluginsManager
     */
    public function getPluginsManager(): PluginsManager
    {
        return $this->pluginsManager;
    }

    /**
     * 设置
     * @param PluginsManager $pluginsManager
     */
    public function setPluginsManager(PluginsManager $pluginsManager): void
    {
        $this->pluginsManager = $pluginsManager;
    }

    /**
     * 调用给定的方法所有的插件
     *
     * @param string $method
     * @param array $arguments
     * @param array $pluginInfo
     * @return mixed|null
     */
    protected function ___callPlugins($method, array $arguments, array $pluginInfo)
    {
/*        // 调用前置拦截器
        if (isset($pluginInfo[InterceptorInterface::LISTENER_BEFORE])) {
            foreach ($pluginInfo[InterceptorInterface::LISTENER_BEFORE] as $code) {
                $pluginInstance = ObjectManager::getInstance($code['instance']);
                $pluginMethod   = $code['method'];
                $beforeResult   = $pluginInstance->$pluginMethod($this, ...array_values($arguments));
                if ($beforeResult !== null) {
                    $arguments = (array)$beforeResult;
                }
            }
        }

        // 调用环绕拦截器
        if (isset($pluginInfo[InterceptorInterface::LISTENER_AROUND])) {
            foreach ($pluginInfo[InterceptorInterface::LISTENER_AROUND] as $code) {
                $pluginInstance = ObjectManager::getInstance($code['instance']);
                $pluginMethod   = $code['method'];
                $result         = $pluginInstance->$pluginMethod($this, ...array_values($arguments));
                if ($result !== null) {
                    $arguments = (array)$result;
                }
            }
        } else {
            // 调用原始方法
            $result = $this->___callParentMethod($method, $arguments);
        }
        if (isset($currentPluginInfo[InterceptorInterface::LISTENER_AFTER])) {
            // 调用后置拦截器
            foreach ($currentPluginInfo[InterceptorInterface::LISTENER_AFTER] as $code) {
                $pluginInstance = ObjectManager::getInstance($code['instance']);
                $pluginMethod   = $code['method'];
                $result         = $pluginInstance->$pluginMethod($this, $result, ...array_values($arguments));
            }
        }

        return $result;*/
         //闭包调用
        $subject = $this;
        $type = $this->subjectType;
        $pluginsManager = $this->pluginsManager;

        $next = function (...$arguments) use (
            $method,
            &$pluginInfo,
            $subject,
            $type,
            $pluginsManager,
            &$next
        ) {
            $capMethod = ucfirst($method);
            $currentPluginInfo = $pluginInfo;
            $result = null;
            if (isset($currentPluginInfo[InterceptorInterface::LISTENER_BEFORE])) {
                // 调用前置拦截器
                foreach ($currentPluginInfo[InterceptorInterface::LISTENER_BEFORE] as $code) {
                    $pluginInstance = ObjectManager::getInstance($code['instance']);
                    $pluginMethod = 'before' . $capMethod;
                    $beforeResult = $pluginInstance->$pluginMethod($this, ...array_values($arguments));

                    if ($beforeResult !== null) {
                        $arguments = (array)$beforeResult;
                    }
                }
            }

            if (isset($currentPluginInfo[InterceptorInterface::LISTENER_AROUND])) {
                // 调用环绕拦截器
                foreach ($currentPluginInfo[InterceptorInterface::LISTENER_AROUND] as $code) {
                    $pluginInstance = ObjectManager::getInstance($code['instance']);
                    $pluginMethod = 'around' . $capMethod;
                    $result = $pluginInstance->$pluginMethod($subject, $next, ...array_values($arguments));
                }
                $pluginInfo = [];
//                $code = $currentPluginInfo[InterceptorInterface::LISTENER_AROUND];
//                $pluginInfo = $pluginsManager->getNext($type, $method, $code);
//                $pluginInstance = ObjectManager::getInstance($code['instance']);
//                $pluginMethod = 'around' . $capMethod;
//                $result = $pluginInstance->$pluginMethod($subject, $next, ...array_values($arguments));
            } else {
                // 调用原始方法
                $result = $subject->___callParentMethod($method, $arguments);
            }
            if (isset($currentPluginInfo[InterceptorInterface::LISTENER_AFTER])) {
                // 调用后置拦截器
                foreach ($currentPluginInfo[InterceptorInterface::LISTENER_AFTER] as $code) {
                    $pluginInstance = ObjectManager::getInstance($code['instance']);
                    $pluginMethod = 'after' . $capMethod;
                    $result = $pluginInstance->$pluginMethod($subject, $result, ...array_values($arguments));
                }
            }
            return $result;
        };

        $result = $next(...array_values($arguments));
        $next = null;

        return $result;
    }

    public function ___callParentMethod(string $method, array $arguments)
    {
        return parent::$method(...$arguments);
    }
}
