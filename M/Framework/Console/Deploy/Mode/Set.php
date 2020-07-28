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

class Set extends CommandAbstract
{

    public function execute($args = array())
    {
        array_shift($args);
        $param = array_shift($args);
        switch ($param) {
            case 'prod':
            case 'dev':
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
}