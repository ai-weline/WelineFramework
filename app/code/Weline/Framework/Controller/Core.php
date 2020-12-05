<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Controller;

use Weline\Framework\Http\Request;
use Weline\Framework\Output\Debug\Printing;

class Core implements Data\DataInterface
{
    protected Request $_request;

    protected Printing $_debug;

    public function noRouter()
    {
        return $this->getRequest()->getResponse()->noRouter();
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
