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

    private mixed $_module;

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
     * @DESC          # 设置模块名
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/11/11 15:55
     * 参数区：
     *
     * @param mixed $module
     *
     * @return $this
     */
    function setModuleInfo(mixed $module): static
    {
        $this->_module = $module;
        return $this;
    }

    /**
     * @DESC          # 获取模块名
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/11/11 15:55
     * 参数区：
     * @return string
     */
    function getModule(): mixed
    {
        return $this->_module;
    }

    /**
     * @DESC          # 获取URL
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/11/6 21:25
     * 参数区：
     *
     * @param string $path
     *
     * @return string
     */
    function getUrl(string $path = ''): string
    {
        if (!isset($this->_url)) {
            $this->_url = ObjectManager::getInstance(Url::class);
        }
        return $this->_url->build($path);
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        if (!isset($this->_request)) {
            $this->_request = ObjectManager::getInstance(Request::class);
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


    #[\JetBrains\PhpStorm\ArrayShape(['msg' => 'string', 'data' => 'mixed|string', 'code' => 'int'])]
    public function success(string $msg = '请求成功！', mixed $data = '', int $code = 200): array
    {
        return ['msg' => __($msg), 'data' => $data, 'code' => $code];
    }

    #[\JetBrains\PhpStorm\ArrayShape(['msg' => 'string', 'data' => 'mixed|string', 'code' => 'int'])]
    public function error(string $msg = '请求失败！', mixed $data = '', int $code = 404): array
    {
        return ['msg' => __($msg), 'data' => $data, 'code' => $code];
    }


    #[\JetBrains\PhpStorm\ArrayShape(['msg' => "string", 'data' => "\Exception", 'code' => "int"])]
    public function exception(\Exception $exception, int $code = 403): array
    {
        return ['msg' => '请求异常！', 'data' => DEV ? $exception : $exception->getMessage(), 'code' => $code];
    }
}
