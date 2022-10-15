<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Controller;

use Weline\Framework\App\Exception;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Debug\Printing;

class Core implements Data\DataInterface
{
    protected ObjectManager $_objectManager;

    protected Request $request;

    protected Printing $_debug;

    private mixed $_module;

    public function noRouter()
    {
        $this->getRequest()->getResponse()->noRouter();
    }


    public function __init()
    {
        if (!isset($this->request)) {
            $this->request = ObjectManager::getInstance(Request::class);
        }
        $this->getObjectManager();
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
    public function setModuleInfo(mixed $module): static
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
    public function getModule(): mixed
    {
        return $this->_module;
    }


    /**
     * @return ObjectManager
     * @throws Exception
     * @throws \ReflectionException
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
        return ['msg' => $msg, 'data' => $data, 'code' => $code];
    }

    #[\JetBrains\PhpStorm\ArrayShape(['msg' => 'string', 'data' => 'mixed|string', 'code' => 'int'])]
    public function error(string $msg = '请求失败！', mixed $data = '', int $code = 404): array
    {
        return ['msg' => $msg, 'data' => $data, 'code' => $code];
    }


    #[\JetBrains\PhpStorm\ArrayShape(['msg' => "string", 'data' => "\Exception", 'code' => "int"])]
    public function exception(\Exception $exception, string $msg = '请求失败！', mixed $data = '', int $code = 403): mixed
    {
        $return_data['data']      = $data;
        $return_data['exception'] = DEV ? $exception : $exception->getMessage();
        return ['msg' => __('请求异常！'), 'data' => $return_data, 'code' => $code];
    }
}
