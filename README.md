
# WelineFramework

#### 介绍
测试环境：https://weline-framework-dev.aiweline.com/ 随时在开发测试，有可能会访问不了
测试后台：https://weline-framework-dev.aiweline.com/admin_123/admin

微蓝WelineFramework框架！
~~~
├── app                 # 应用目录
│   ├── code            # -代码
│   ├── design          # -主题
│   ├── etc             # -配置
│   └── i18n            # -语言包
├── bin                 # 命令目录
├── dev                 # 开发目录
├── extend              # 拓展
├── generated           # 系统自动生成目录
│   ├── code            # -代码
│   ├── language        # -语言
│   └── routers         # -路由
├── pub                 # 公共
│   ├── errors          # -错误文件存放目录
│   ├── readme          # -关于
│   └── static          # -静态文件
├── setup               # 升级安装
│   ├── readme          # -关于
│   ├── static          # -升级安装时的静态目录
│   └── step            # -升级代码
├── var                 # 数据存放目录
│   ├── cache           # -缓存目录【仅文件缓存使用】
│   ├── log             # -日志目录
│   └── session         # -Session存放目录【仅文件session使用】
└── vendor              # Composer第三方拓展目录
~~~
#### 软件架构

    PHP>=8.1
    composer
    nginx/apache
    mysql

#### 安装教程
composer下载源码
~~~
composer create-project aiweline/weline-framework WelineFramework --prefer-dist
~~~
###一、项目安装

    1.  WEB项目部署
    2.  无需设置繁杂的nginx（项目中有样例设置，include到配置中就可以）或者Apache设置（针对Apache项目中编写有伪静态），仅设置项目目录为部署目录即可。

###二、框架命令

    1.  模块安装命令 bin/m module:upgrade 此命令更新安装模块，以及模块数据。（将执行模块中的Setup\Install.php卸载脚本）
    2.  模块安装命令 bin/m module:disable <module_name> 此命令更新安装模块，以及模块数据。（将执行模块中的Setup\Install.php卸载脚本）
    3.  模块卸载命令 bin/m module:remove <module_name> 此命令备份模块并删除模块。（将执行模块中的Setup\Remove.php卸载脚本）
    4.  其他命令 php bin/m 回车可见

### 框架目的
开发优雅且高可用的框架：主要框架使用更加人性化，简单，灵活，快速。

### 框架特性
跨平台支持：Windows/linux。

##1、自带后台

#1) acl权限管理。

get,post,delete,update等方法精细级别访问控制器。

#2）url管理。
实现任何链接seo自由重写。

#3) i18n全球化词典管理。
可自行安装国家地区，并收集前端词典进行翻译，运营人员即可完成翻译，也可以自行开发对接第三方api做自动化翻译。

4）缓存控制器。分类型缓存管理，可以单独针对某个缓存进行管理。

5）计划任务管理。收集管理各个模块中的计划任务，可实现解锁，上锁运行等操作。计划任务支持window和linux.

6）事件管理。可以轻松查看正在运行的事件。

7）插件管理。可以查看插件位置。

8）模组管理。实时查看和禁用组件。

9）SMTP管理。配置邮件SMTP。

10）开发配置。内置开发文档，方便开发者查阅开发资料。内置两套开发模板，分别是前端和后端模板，可以快速成型项目。

11）内容管理。设计运营人员可以自定义cms页面，将支持前端模板和php代码直接在后台编写，实现ajax解析前端模板变量形成可预览页面。新增发布版本控制。（建设中...）

12）网站内测机制。url添加debug参数将进入金丝雀机制，产生的数据将进入测试系统，不会污染正式系统，最好搭配ip段实现（建设中...）


2、ORM

1）Model模型操作。Model模型使用魔术方法改造成查询器和数据容器，简化orm操作难度，自带归档数据，自带数据分页，自带树形结构数据返回函数，自解析表名，快速join，自定义附加sql,可在查询过程中定义复杂高级操作。

2）Model模型数据源。支持框架一主多从作为数据源，也支持Model模型所在模组一主多从作为数据源。也就是Model可以从多个指定数据库读取数据，而非单一的从框架主库配置的数据库池子中读取，它可以有自己的数据库池。

3）Model模型读写分离。可以从给定的主从数据库中读写分离。目前算法是随机算法，并未加入均衡器算法。


3、自定义高性能模板渲染。

1）tab标签。支持常用的lang,if,foreach,else,block,template等,支持形式：<block .../>,@lang(...),@lang{...}。可以用事件自定义标签。

2）缓存去标签化。标签一旦解析成为缓存模板【全部由php代码和html代码组成】，不会存在任何标签痕迹，下次读取时也不会再次解析【开发者环境下会一直读取】。

3）模板去翻译化。语言由标签解析环节就生效，并存储到不同的语言目录，无需PHP代码再次翻译。减少PHP翻译过程。【实时翻译环境下会一直翻译】

4）前端Hook机制。可以在页面中植入钩子，例如：<hook>head_after</hook>，模板引擎会自动解析这个钩子。


4、容器

1）简化实例化过程。且附带实例化执行，自动解析初始化函数依赖，无需使用new ClassName().可以在__construct(\Weline\Demo\Model\Demo $demo)直接实例化$demo.

2）依赖PHP8的注解解析。协助acl解析类或者方法注解，实现注解可直接执行。给出事件，方便控制做类型解析时解析或者执行注解类。作用，注解类直接执行可以实现参数检测，登录检测等快速检测。


5、预置命令

协调管理框架，具体可以php bin/m 查看所有命令和使用方法。

6、主题Theme。

可以复写所有module中的模板，轻松实现自定义主题。


#### 使用说明

    
    
#### 更新说明

#### 参与贡献

1.  Fork 本仓库
2.  新建 Feat_xxx 分支
3.  提交代码
4.  新建 Pull Request

