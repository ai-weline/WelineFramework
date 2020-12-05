<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Controller;

use Aiweline\HelloWorld\Model\AiwelineHelloWorld;
use Weline\Framework\App\Cache;
use Weline\Framework\App\Controller\FrontendController;
use Weline\Framework\App\Exception;
use Weline\Framework\App\Session\FrontendSession;

class HelloWorld extends FrontendController
{
    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @throws \Weline\Framework\App\Exception
     * @return bool
     */
    public function index()
    {
        $method = $this->_request->getMethod();
        $assign = [
            'core'   => 'M Framework',
            'method' => $method,
            'module' => [
                'name' => $this->_request->getModuleName(),
                'path' => $this->_request->getModulePath(),
            ],
        ];
        $this->assign($assign);

        return $this->fetch();
//        return $this->fetch('test/ok');
    }

    /**
     * @DESC         |p函数调试信息
     *
     * 参数区：
     */
    public function p()
    {
        p('Hello p($data)!');
    }

    /**
     * @DESC         |异常调试
     *
     * 参数区：
     */
    public function ex()
    {
        throw new Exception('Hello Exception!');
    }

    public function model()
    {
        $model = new AiwelineHelloWorld();
        p('链接类型：' . $model->getDb()->getConfig('default'), 1);
        $data = $model->getDb()->query("select * from {$model->getTable()}");
        p($data);
        p($model->insert([
            'demo' => 1,
        ]));
    }

    public function demo()
    {
        $model = new AiwelineHelloWorld();
        $data  = $model->getDb()->query("select * from {$model->getTable()}");
        if (empty($data)) {
            $model->insert([
                'demo' => 1,
            ]);
        }
        $data = $model->getDb()->query("select * from {$model->getTable()}");
        $this->assign('data', $data);
        $this->fetch();
    }

    /**
     * @DESC         |session测试
     *
     * 参数区：
     */
    public function session()
    {
        $frontSession = new FrontendSession();
        p('是否登录:' . ($frontSession->isLogin() ? '是' : '否'), 1);
        $session = $frontSession->getSession();
        $session->set('test', 123);
        p($session->get('test'));
    }

    /**
     * @DESC         |cache测试
     *
     * 参数区：
     */
    public function cache()
    {
        $cache = (new Cache())->cache();
        $cache->set('111', 8888);
        p($cache->get('111'));
    }
}
