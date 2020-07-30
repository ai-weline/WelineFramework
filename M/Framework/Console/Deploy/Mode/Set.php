<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/28
 * 时间：21:10
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\Deploy\Mode;


use M\Framework\App\Env;
use M\Framework\Console\CommandAbstract;
use M\Framework\Console\Deploy\Upgrade;
use M\Framework\FileSystem\App\Scanner as AppScanner;
use M\Framework\View\Data\DataInterface;

class Set extends CommandAbstract
{

    public function execute($args = array())
    {
        array_shift($args);
        $param = array_shift($args);
        switch ($param) {
            case 'prod':
                $this->printer->note('正在清除模组模板编译文件...');
                $this->cleanTplComDir();
                $this->printer->note('正在执行静态资源部署...');
                $deploy_upgrade = new Upgrade();
                $deploy_upgrade->execute();
                break;
            case 'dev':
                $this->printer->note('正在清除模组模板编译文件...');
                $this->cleanTplComDir();
                break;
            default:
                $this->printer->error(' ╮(๑•́ ₃•̀๑)╭  ：错误的部署模式：' . $param);
                $this->printer->note('(￢_￢) ->：允许的部署模式：dev/prod');
                return;
        }
        if (Env::getInstance()->setConfig('deploy', $param)) {
            $this->printer->success('（●´∀｀）♪ 当前部署模式：' . $param);
        } else {
            $this->printer->error('╮(๑•́ ₃•̀๑)╭ 部署模式设置错误：' . $param);
        }
    }

    public function getTip(): string
    {
        return '部署模式设置。（dev:开发模式；prod:生产环境。）';
    }

    /**
     * @DESC         |清理模块编译目录
     *
     * 参数区：
     *
     */
    protected function cleanTplComDir(){
        // 扫描代码
        $scanner = new AppScanner();
        $apps = $scanner->scanAppModules();
        // 注册模块
        foreach ($apps as $vendor => $modules) {
            foreach ($modules as $name => $register) {
                $this->printer->note($vendor . '_' . $name . '...');
                $module_view_tpl_com_dir = APP_PATH . $vendor . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . DataInterface::dir . DIRECTORY_SEPARATOR . DataInterface::view_TEMPLATE_COMPILE_DIR . DIRECTORY_SEPARATOR;
                if (is_dir($module_view_tpl_com_dir)) {
                    exec("rm $module_view_tpl_com_dir -r");
                }
            }
        }
    }
}