<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Controller;

use Weline\Framework\Http\Request;
use Weline\Framework\Http\Url;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Debug\Printing;
use Weline\Framework\Session\SessionInterface;
use Weline\Framework\Session\Session;

class Core implements Data\DataInterface
{
    protected ObjectManager $_objectManager;

    protected Request $_request;
    protected SessionInterface $_session;

    protected Printing $_debug;
    protected ?Url $_url = null;

    public function noRouter()
    {
        $this->getRequest()->getResponse()->noRouter();
    }


    public function __init()
    {
        $this->getObjectManager();
        $this->getRequest();
    }

    function getSession(string $session_class_name = null): SessionInterface
    {
        if (!isset($this->_session)) {
            $this->_session = $this->getObjectManager()->getInstance($session_class_name ?? Session::class);
        }
        return $this->_session;
    }

    /**
     * @DESC          # 获取URL
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/11/6 21:25
     * 参数区：
     * @param string $path
     * @return string
     */
    function getUrl(string $path = ''): string
    {
        if (!isset($this->_url)) {
            $this->_url = $this->_objectManager::getInstance(Url::class);
        }
        return $this->_url->build($path);
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        if (!isset($this->_request)) {
            $ctl_class = explode('\\', get_class($this));
            $module_path = array_shift($ctl_class) . '\\' . array_shift($ctl_class);
            $this->_request = Request::getInstance($module_path);
        }

        return $this->_request;
    }

    /**
     * @return Request
     */
    public function getObjectManager(): ObjectManager
    {
        if (!isset($this->_objectManager)) {
            $this->_objectManager = ObjectManager::getInstance();
        }

        return $this->_objectManager;
    }

    /**
     * @return Printing
     */
    public function getDebug(): Printing
    {
        if (!isset($this->_debug)) {
            $this->_debug = new Printing();
        }

        return $this->_debug;
    }
}
