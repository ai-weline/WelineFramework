<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\App\Controller;

use Weline\Framework\App\Session\FrontendApiSession;
use Weline\Framework\Controller\AbstractRestController;
use Weline\Framework\Manager\ObjectManager;

class FrontendRestController extends AbstractRestController
{
    private ?FrontendApiSession $session = null;

    function __init()
    {
        parent::__init();
        $this->getSession();
    }

    function getSession()
    {
        if (!$this->session) {
            $this->session = ObjectManager::getInstance(FrontendApiSession::class);
        }
        return $this->session;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $msg
     * @param bool $data
     * @param int $code
     */
    public function error($msg = '错误！', $data = false, int $code = 400)
    {
        die($this->fetch(['msg' => $msg, 'data' => $data, 'code' => $code]));
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $msg
     * @param bool $data
     * @param int $code
     */
    public function errorXml($msg = '错误！', $data = false, int $code = 400)
    {
        die($this->fetch(['msg' => $msg, 'data' => $data, 'code' => $code]));
    }
}
