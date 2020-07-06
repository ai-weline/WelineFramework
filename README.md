# WelineFramework

#### 介绍
微蓝M框架！

1、代码可移植性。
代码可安装到其他同框架的项目中。一个代码应用目录位置位于项目下的app/code中。模块中可设置Api目录,Controller目录，view目录等以及必须的register.php注册文件。
其中的每个应用可以移植安装。

2、无缝集成TP6的ORM,更加符合国人开发逻辑。

3、前后端集成到一个module中，做到一个需求一个module。

#### 软件架构
PHP>=7.4(强类型编写)
composer
nginx/apache
#### 安装教程

1.  WEB项目部署
2.  无需设置繁杂的nginx设置，仅设置项目目录为部署目录即可。
3.  模块安装命令 bin/m module:upgrade 此命令更新安装模块，以及模块数据。（将执行模块中的Setup\Install.php卸载脚本）
4.  模块卸载命令 bin/m module:remove 此命令删除模块。（将执行模块中的Setup\Remove.php卸载脚本）

#### 使用说明

1.  应用模块化
2.  bin/m 命令
3.  xxxx

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
