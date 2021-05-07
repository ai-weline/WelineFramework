<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Controller;

use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Debug\Printing;

class Core implements Data\DataInterface
{
    protected ObjectManager $_objectManager;

    protected Request $_request;

    protected Printing $_debug;

    public function noRouter()
    {
        return $this->getRequest()->getResponse()->noRouter();
    }

    public function __init()
    {
        $this->getRequest();
        $this->getObjectManager();
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        if (! isset($this->_request)) {
            $ctl_class      = explode('\\', get_class($this));
            $module_path    = array_shift($ctl_class) . '\\' . array_shift($ctl_class);
            $this->_request = Request::getInstance($module_path);
        }

        return $this->_request;
    }

    /**
     * @return Request
     */
    public function getObjectManager(): ObjectManager
    {
        if (! isset($this->_objectManager)) {
            $this->_objectManager = ObjectManager::getInstance();
        }

        return $this->_objectManager;
    }

    /**
     * @return Printing
     */
    public function getDebug(): Printing
    {
        if (! isset($this->_debug)) {
            $this->_debug = new Printing();
        }

        return $this->_debug;
    }
}
