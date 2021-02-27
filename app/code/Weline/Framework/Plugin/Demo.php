<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Plugin;

class Demo
{
}

class Action
{
    public function perform()
    {
        echo 'hello,fanyh!<br>';
    }
}

/**
 * 定义一个Interceptor
 * @author Administrator
 */
class InterceptorImpl1 extends AbstractInterceptor
{
    public function doBefore()
    {
        echo 'Before method......111111111111111111<br>';
    }

    public function doAfter()
    {
        echo 'After method......1111111111111111111<br>';
    }
}

/**
 * 定义一个Interceptor
 * @author Administrator
 */
class InterceptorImpl2 extends AbstractInterceptor
{
    public function doBefore()
    {
        echo 'Before method......2222222222222<br>';
    }

    public function doAfter()
    {
        echo 'After method......22222222222222222<br>';
    }
}

/**
 * 控制器类,同时作为Interceptor的容器
 * @author Administrator
 */
class Controller
{
    private $interceptors = [];

    private $index = 0;

    /**
     * 调用Interceptor中的方法来执行
     */
    public function invoke()
    {
        if ($this->index < count($this->interceptors)) {
            $this->interceptors[$this->index++]->invoke($this, 'invoke');
        } else {
            $this->index = 0;
            $action      = new Action();
            $action->perform();
        }
    }

    /**
     * 增加Interceptor
     * @param unknown_type $interceptor
     */
    public function addInterceptor($interceptor)
    {
        $this->interceptors[] = $interceptor;
    }
}

$controller = new Controller();
$controller->addInterceptor(new InterceptorImpl1());
$controller->addInterceptor(new InterceptorImpl2());
$controller->invoke();
