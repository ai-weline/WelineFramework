<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Controller;

use Aiweline\HelloWorld\Model\AiwelineHelloWorld;
use Aiweline\HelloWorld\Model\PluginTestModel;
use Weline\Framework\App\Cache;
use Weline\Framework\App\Controller\FrontendController;
use Weline\Framework\App\Exception;
use Weline\Framework\App\Session\FrontendSession;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;

class HelloWorld extends FrontendController
{
    private Cache $cache;

    private AiwelineHelloWorld $aiwelineHelloWorld;

    private FrontendSession $frontendSession;

    private PluginTestModel $pluginTestModel;

    /**
     * @var EventsManager
     */
    private EventsManager $eventsManager;

    public function __construct(
        Cache $cache,
        AiwelineHelloWorld $aiwelineHelloWorld,
        FrontendSession $frontendSession,
        PluginTestModel $pluginTestModel,
        EventsManager $eventsManager
    ) {
        $this->cache              = $cache;
        $this->aiwelineHelloWorld = $aiwelineHelloWorld;
        $this->frontendSession    = $frontendSession;
        $this->pluginTestModel    = $pluginTestModel;
        $this->eventsManager      = $eventsManager;
    }

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
        p('链接类型：' . $this->aiwelineHelloWorld->getDb()->getConfig('default'), 1);
        $data = $this->aiwelineHelloWorld->getDb()->query("select * from {$this->aiwelineHelloWorld->getTable()}");
        p($data);
        p($this->aiwelineHelloWorld->insert([
            'demo' => 1,
        ]));
    }

    public function demo()
    {
        $data = $this->aiwelineHelloWorld->getDb()->query("select * from {$this->aiwelineHelloWorld->getTable()}");
        if (empty($data)) {
            $this->aiwelineHelloWorld->insert([
                'demo' => 1,
            ]);
        }
        $data = $this->aiwelineHelloWorld->getDb()->query("select * from {$this->aiwelineHelloWorld->getTable()}");
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
        $frontSession = $this->frontendSession;
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
        $cache = ObjectManager::getInstance(Cache::class)->cache();
        $cache->set('111', 8888);
        p($cache->get('111'));
    }

    public function observer()
    {
        // 分配事件
        $a = new DataObject(['a' => 1]);
        p($a->getData('a'), 1);
        $this->eventsManager->dispatch('Aiweline_Index::test_observer', ['a' => $a]);
        p($a->getData('a'));

        return $this->fetch();
    }

    public function plugin()
    {
        $a[]              = '默认插件';
        $plugin_deal_data = $this->pluginTestModel->getName($a);
        p('插件修改的类：' . get_class($this->pluginTestModel), 1);
        p($plugin_deal_data);
    }
}
