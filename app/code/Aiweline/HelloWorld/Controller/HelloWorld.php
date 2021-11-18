<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
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

    /**
     * @var EventsManager
     */
    private EventsManager $eventsManager;

    public function __construct(
        Cache $cache,
        AiwelineHelloWorld $aiwelineHelloWorld,
        FrontendSession $frontendSession,
        EventsManager $eventsManager
    ) {
        $this->cache              = $cache;
        $this->aiwelineHelloWorld = $aiwelineHelloWorld;
        $this->frontendSession    = $frontendSession;
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
        p('链接名：' . $this->aiwelineHelloWorld->getConnection()->getConfigProvider()->getConnectionName(), 1);
        p('链接类型：' . $this->aiwelineHelloWorld->getConnection()->getConfigProvider()->getDbType(), 1);
        $data = $this->aiwelineHelloWorld->query("select * from {$this->aiwelineHelloWorld->getTable()}")->fetch();
        p($data,1);
        p($this->aiwelineHelloWorld->insert([
            'demo' => 1,
        ])->fetch());
    }

    public function demo()
    {
        $data = $this->aiwelineHelloWorld->query("select * from {$this->aiwelineHelloWorld->getTable()}")->fetch();
        if (empty($data)) {
            $this->aiwelineHelloWorld->insert([
                'demo' => 1,
            ])->fetch();
        }
        $data = $this->aiwelineHelloWorld->query("select * from {$this->aiwelineHelloWorld->getTable()}")->find()->fetch();
        $this->assign('data', $data->getData());
        $this->fetch();
    }

    /**
     * @DESC         |session测试
     *
     * 参数区：
     */
    public function session()
    {
        p('是否登录:' . ($this->frontendSession->isLogin() ? '是' : '否'), 1);
        $this->frontendSession->setData('test', 123);
        p($this->frontendSession->getData('test'));
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
        p('需要注意的是：本身插件的实现是利用语法糖的数组参数，如果传输数组会被解析成参数！建议所有传输数据请使用DataObject包裹', 1);
        $a = '默认插件1';
        $a .= '默认插件2';
        /**@var PluginTestModel $pluginTestModel*/
        $pluginTestModel  = $this->_objectManager::getInstance(PluginTestModel::class);
        $plugin_deal_data = $pluginTestModel->getName($a);
        p('插件修改的类：' . str_replace('\Interceptor', '', get_class($pluginTestModel)), 1);
        p($plugin_deal_data);
    }

    public function i18n()
    {
        p($this->getRequest()->getModuleName() . '模块i18n目录下的‘zh_Hans_CN.csv’原文："你好i18n！"', 1);
        p('经过函数__("你好i18n！");翻译后：' . __('你好i18n！'), 1);
        p('语言包目录app/i18n/下的Weline/zh_Hans_CN/zh_Hans_CN.csv 原文："你好翻译包i18n！"', 1);
        p('经过函数__("你好翻译包i18n！");翻译后：' . __('你好翻译包i18n！'), 1);
        p('翻译包的优先级低于模块下i18n定义的翻译，也就是模块下的i18n将覆盖语言包');
    }
}
