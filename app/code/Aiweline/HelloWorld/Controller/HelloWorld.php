<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/27
 * 时间：0:12
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\HelloWorld\Controller;


use Aiweline\HelloWorld\Model\AiwelineHelloWorld;
use M\Framework\App\Cache;
use M\Framework\App\Controller\FrontendController;
use M\Framework\App\Exception;
use M\Framework\App\Session;

class HelloWorld extends FrontendController
{
    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return bool
     * @throws \M\Framework\App\Exception
     */
    function index()
    {
        $method = $this->_request->getMethod();
        $assign = array(
            'core' => 'M Framework',
            'method' => $method,
            'module' => array(
                'name' => $this->_request->getModuleName(),
                'path' => $this->_request->getModulePath()
            )
        );
        $this->assign($assign);
        return $this->fetch();
//        return $this->fetch('test/ok');
    }

    /**
     * @DESC         |p函数调试信息
     *
     * 参数区：
     *
     */
    function p()
    {
        p('Hello p($data)!');
    }

    /**
     * @DESC         |异常调试
     *
     * 参数区：
     *
     */
    function ex()
    {
        throw new Exception('Hello Exception!');
    }

    function model(){
        $model = new AiwelineHelloWorld();
        $data = $model->getDb()->query("select * from {$model->getTable()}");
        p($data);
        p($model->insert([
            'demo'=>1
        ]));
    }

    function demo(){
        $model = new AiwelineHelloWorld();
        $data = $model->getDb()->query("select * from {$model->getTable()}");
        if(empty($data)) $model->insert([
            'demo'=>1
        ]);
        $data = $model->getDb()->query("select * from {$model->getTable()}");
        $this->assign('data',$data);
        $this->fetch();
    }

    /**
     * @DESC         |session测试
     *
     * 参数区：
     *
     */
    function session(){
        $session = (new Session())->session();
        $session->set('test',56);
        p($session->get('test'));
    }

    /**
     * @DESC         |cache测试
     *
     * 参数区：
     *
     */
    function cache(){
        $cache = (new Cache())->cache();
        $cache->set('111',8888);
        p($cache->get('111'));
    }
}