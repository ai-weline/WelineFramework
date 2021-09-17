
# WelineFramework

#### 介绍

微蓝WelineFramework框架！

测试环境：http://m.dev.aiweline.com/
~~~
注意：dev开发环境下把防跨站攻击关闭。

~~~

样本环境：http://m.aiweline.com/

    1、代码可移植性。
    代码可安装到其他同框架的项目中。一个代码应用目录位置位于项目下的app/code中。模块中可设置Api目录,Controller目录，view目录等以及必须的register.php注册文件。
    其中的每个应用可以移植安装。
    
    2、自定义ORM:
      1、Model开发模式下setup,可实现轻松对表进行修改操作，无需直接操作数据库；
      2、一个Model模型可以query查询，也可当做一个数据的对象;
      3、Orm兼容适配器模式，可以适配其他数据库。
    
    3、前后端集成到一个module中，做到一个需求一个module。
    
    4、代码模块化，接口以及传统路由分前后台。包括接口，具有后台接口入口，后台url入口。
    
    5、配置文件统一化。文件位置：app/etc/env.php

    6、多主题继承模组Module文件。

#### 软件架构

    PHP>=8.0
    composer
    nginx/apache

#### 安装教程
###一、项目安装

    1.  WEB项目部署
    2.  无需设置繁杂的nginx（项目中有样例设置，include到配置中就可以）或者Apache设置（针对Apache项目中编写有伪静态），仅设置项目目录为部署目录即可。

###二、框架命令

    1.  模块安装命令 bin/m module:upgrade 此命令更新安装模块，以及模块数据。（将执行模块中的Setup\Install.php卸载脚本）
    2.  模块安装命令 bin/m module:disable <module_name> 此命令更新安装模块，以及模块数据。（将执行模块中的Setup\Install.php卸载脚本）
    3.  模块卸载命令 bin/m module:remove <module_name> 此命令备份模块并删除模块。（将执行模块中的Setup\Remove.php卸载脚本）
    4.  其他命令 php bin/m 回车可见

#### 使用说明

    1.  框架可快速实现Rest Api,前后端。
    2.  后期会自带开发文档模块。
    
    
#### 更新说明

#2.0
1、i18n国际化翻译。
2、事件管理可直接传非对象常值，无需构建对象（完成事件Wrapper）。
3、

#V1.0
1.  配置xml化
2.  新增事件观察者机制（event.xml)
3.  新增命令简化机制.
    例如之前运行：php bin/m deploy:mode:set dev 
    现在仅需要运行：php bin/m d:m:se dev
    简单来说就是现在运行命令可以使首字母匹配的方式，比如deploy可以简写匹配成d或者de再或者dep等，如果匹配冲突会提示相关命令。
![输入图片说明](https://images.gitee.com/uploads/images/2021/0124/182852_fd7f82a9_1568278.png "微信截图_20210124182835.png")
4. 框架缓存系统完成！
![输入图片说明](https://images.gitee.com/uploads/images/2021/0124/220301_22ae5546_1568278.png "微信截图_20210124220214.png")
5. 事件Event观察者Observer模式

        详情请转到开发站查看如何使用：http://m.dev.aiweline.com/index/observer
        // 分配事件...
        $a = new DataObject(['a' => 1]);
        p($a->getData('a'),1);
        $this->eventsManager->dispatch('Aiweline_Index::test_observer', ['a' => $a]);
        p($a->getData('a'));
        // 观察者注册
        etc/event.xml
        <?xml version="1.0"?>
        <config xmlns:xs="http://www.w3.org/2001/XMLSchema-instance"
                xs:noNamespaceSchemaLocation="urn:Weline_Framework::Event/etc/xsd/event.xsd"
                xmlns="urn:Weline_Framework::Event/etc/xsd/event.xsd">
            <event name="Aiweline_Index::test_observer">
                <observer name="bbs_test" instance="Aiweline\Bbs\Observer\Test" disabled="false" shared="true"/>
            </event>
        </config>
        // 观察者
        class Test implements ObserverInterface
        {
            public function execute(Event $event)
            {
                $a = $event->getData('a');
                p('我是观察者',1);
                $a->setData('a', 2);
            }
        }


6. 插件机制

~~~
第一步：模块的etc目录下plugin.xml,参考：Aiweline_HelloWorld模块
<?xml version="1.0"?>
<config xmlns:xs="http://www.w3.org/2001/XMLSchema-instance"
        xs:noNamespaceSchemaLocation="urn:Weline_Framework::Plugin/etc/xsd/plugin.xsd"
        xmlns="urn:Weline_Framework::Plugin/etc/xsd/plugin.xsd">
    <plugin name="HelloWorld::controller_plugin" class="Aiweline\HelloWorld\Model\PluginTestModel">
        <interceptor name="HelloWorld::interceptor_index_test1" instance="Aiweline\HelloWorld\Plugin\PluginTestModel" disabled="false" sort="2"/>
        <interceptor name="HelloWorld::interceptor_index_test2" instance="Aiweline\HelloWorld\Plugin\PluginTest" disabled="false" sort="2"/>
    </plugin>
</config>

简要说明：
class属性是要拦截的类
interceptor元素是拦截器定义
instance是拦截类


第二步：实现拦截器类

Aiweline\HelloWorld\Plugin\PluginTestModel
示例代码：
<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\HelloWorld\Plugin;

class PluginTestModel
{
    public function beforeGetName($object, $a)
    {
        $a[] = '我是beforeGetName修改过的插件';

        return $a;
    }

    public function aroundGetName($object, \closure $closure, $a)
    {
        $a[] = '我是aroundGetName修改过的插件';

        return $a;
    }

    public function afterGetName($object, $a)
    {
        $a[] = '我是afterGetName修改过的插件';

        return $a;
    }
}

第三步 生成拦截器拦截者（插件系统依赖编译）
项目下执行
php bin/m plugin:di:compile
或者
php bin/m p:d:c
~~~
![输入图片说明](https://images.gitee.com/uploads/images/2021/0318/234622_02e3c50a_1568278.png "微信截图_20210318234608.png")
~~~
第四步 访问测试
具体可看helloword模块，http://m.aiweline.com也可以查看插件测试。
~~~
![输入图片说明](https://images.gitee.com/uploads/images/2021/0318/234828_d69ffba1_1568278.png "微信截图_20210318234816.png")


7、完成主题功能

所有主题可继承默认所有模组文件，示例位于code/design目录下。

8、完成i18n翻译功能

已完成！

9、对象缓存

已完成。

10、新增工厂类模式

~~~
实例化一个类，如果类后缀是Factory时会创建类并检查create函数，有则运行
例如：
原本实例化\Weline\Framework\Phrase\Cache\PhraseCache后需要再执行create，才能得到缓存驱动。
$phraseCache = (new \Weline\Framework\Phrase\Cache\PhraseCache())->create();
这样会很麻烦。
现在使用工厂类模式：
/**@var \Weline\Framework\Cache\CacheInterface $phraseCache */
$phraseCache = ObjectManager::getInstance(\Weline\Framework\Phrase\Cache\PhraseCache::class.'Factory');
或者：
$phraseCache = ObjectManager::getInstance('\Weline\Framework\Phrase\Cache\PhraseCacheFactory');
就能够轻松搞定。
~~~


下一个版本2.0计划
~~~
1、将所有依赖编入metadata元数据,
包括主题文件数据，以免多次判断，最直接的做法是直接读取数据，
也就是用框架代码去生成原始的PHP代码，
url->router->controller(或者静态资源)，
过程中有各种复杂的配置判断，直接设置元数据告诉路由直接读取
加快框架加载速度。
2、路由参数实现
3、driver驱动观察者加入，以获得驱动拓展
4、单个缓存清理
5、module间的继承关系：env.php中定义的继承关系
6、多模块扫描module优化，现分散且重复代码过多（针对文件module扫描的地方）错综复杂且乱，应该提供一个扫描接口，统一扫描文件
7、重要：model模型自定义，抛除TP6的Model
细分项：
数据库部分
（1）Weline\Framework\Database\Db\Ddl\Table表创建添加外键addForeignKey方法。
~~~

正在进行...
1、重要：model模型自定义，抛除TP6的Model

下一个版本2.1计划
~~~
1、观察者wapper,简化观察者new DataObject过程
~~~

下一个版本2.2计划
~~~
1、模组间彻底解耦
~~~

#### 参与贡献

1.  Fork 本仓库
2.  新建 Feat_xxx 分支
3.  提交代码
4.  新建 Pull Request

