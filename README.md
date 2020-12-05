# WelineFramework

#### 介绍
微蓝M框架特性

    1、代码可移植性。
    代码可安装到其他同框架的项目中。一个代码应用目录位置位于项目下的app/code中。模块中可设置Api目录,Controller目录，view目录等以及必须的register.php注册文件。
    其中的每个应用可以移植安装。
    
    2、无缝集成TP6的ORM,更加符合国人开发逻辑。
    
    3、前后端集成到一个module中，做到一个需求一个module。
    
    4、代码模块化，快速接口以及传统路由分前后台。包括接口，具有后台接口入口，后台url入口。
    
    5、配置文件统一化。文件位置：app/etc/env.php
    
    6、支持前端标签：
        <php>                                                     ：标签内的内容为php代码
        @include  header.phtml                                    ：@include独占一行，可实现导入文件
        @static css/style.css                                     ：@static无需独占一行，导入静态资源时使用
        @template component/selector.phtml                        ：@template独占一行，组件导入
        @foreach(forum as k=>v){<li fid="{v.fid}">{v.name}</li>}  ：@foreach独占一行，简单渲染使用
    
    其余标签可使用thinkphp6模板标签！

#### 软件架构
    PHP>=7.4(强类型编写)
    composer
    nginx/apache
#### 安装教程

**一、项目安装**

方式一、命令行安装（**推荐**）

    项目根目录下输入php bin/m system:install:sample返回安装样例如下，修改系统配置后运行安装命令即可。
    **Linux：**
        php bin/m system:install \
        --db-type=mysql \
        --db-hostname=127.0.0.1 \
        --db-database=m_dev \
        --db-username=m_dev \
        --db-password=ShP5T7yzNMs87ZDp
    **Windows:**
        php bin/m system:install ^
        --db-type=mysql ^
        --db-hostname=127.0.0.1 ^
        --db-database=m_dev ^
        --db-username=m_dev ^
        --db-password=ShP5T7yzNMs87ZDp
    
方式二、web安装

    1.  无需设置繁杂的nginx（项目中有样例设置，include到配置中就可以，include 前请“set WELINE_ROOT”项目目录变量）
        或者Apache设置（针对Apache项目中编写有伪静态），仅设置项目目录为部署目录即可。
    2.  部署后访问即可进入安装步骤。

**二、代码安装**

    1.  模块安装命令 bin/m module:upgrade 此命令更新安装模块，以及模块数据。（将执行模块中的Setup\Install.php卸载脚本）
    2.  模块安装命令 bin/m module:disable <module_name> 此命令更新安装模块，以及模块数据。（将执行模块中的Setup\Install.php卸载脚本）
    3.  模块卸载命令 bin/m module:remove <module_name> 此命令备份模块并删除模块。（将执行模块中的Setup\Remove.php卸载脚本）

#### 使用说明

1.  模块仅在WEB环境中可用。
2.  框架目前仅是骨架，后期支持后台，前台。

#### 版本更新
**setup1.1** 2020-12-30 09:39

    1.  更新代码适配composer
    2.  更新框架命名为WelineFramework
    3.  更新基于框架的开发应用BBS
    4.  更新php代码标准插件php-cs-fixer在框架中的应用
    5.  更新win环境下命令兼容
        命令使用linux格式，可自动转换win环境正常执行，例如：rm -f 命令，在win环境下可换为 del /F 命令。
        使用转化执行类：Weline\Framework\App\System 执行函数：exec();
    6.  更新支持设置BaseController,方便设置基础控制器。
    7.  更新修正安装样例代码不可斜杠转行安装（代码遇到php system:install 先行执行，而将后续代码判为询问参数问题）
    
    预版本更新
        1，支持主题
        2，支持后端
        3，支持前端

**setup1.0** 2020-07-30 09:39

    更新核心框架功能。   
#### 参与贡献

1.  Fork 本仓库
2.  新建 Feat_xxx 分支
3.  提交代码
4.  新建 Pull Request




#### 码云特技

1.  使用 Readme\_XXX.md 来支持不同的语言，例如 Readme\_en.md, Readme\_zh.md
2.  码云官方博客 [blog.gitee.com](https://blog.gitee.com)
3.  你可以 [https://gitee.com/explore](https://gitee.com/explore) 这个地址来了解码云上的优秀开源项目
4.  [GVP](https://gitee.com/gvp) 全称是码云最有价值开源项目，是码云综合评定出的优秀开源项目
5.  码云官方提供的使用手册 [https://gitee.com/help](https://gitee.com/help)
6.  码云封面人物是一档用来展示码云会员风采的栏目 [https://gitee.com/gitee-stars/](https://gitee.com/gitee-stars/)
