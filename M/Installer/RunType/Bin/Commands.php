<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/16
 * 时间：1:35
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Installer\RunType\Bin;


use M\Framework\App\Env;
use M\Framework\App\Exception;
use M\Framework\Output\Cli\Printing;
use M\Installer\Helper\Data;

class Commands
{
    protected Printing $printer;
    protected Data $data;

    function __construct()
    {
        $this->printer = new Printing();
        $this->data = new Data();
    }

    function run()
    {
        $hasErr = false;
        $tmp = [];
        foreach ($this->data->getCommands() as $needCommand) {
            try {
                exec('php ' . BP . $needCommand, $result);
                $value = str_pad('✔', 10, " ", STR_PAD_BOTH);
            } catch (Exception $e) {
                $hasErr = true;
                $value = str_pad('✖', 10, " ", STR_PAD_BOTH);
            }
            $key = str_pad('---' . $needCommand, 45, '-', STR_PAD_BOTH);
            if (CLI) $this->printer->success($key . '=>' . $value,'OK');
            $tmp[$key] = $value;
            foreach ($result as $item) {
                $this->printer->printing($item);
            }
            unset($result);
        }
        // 读取后台以及接口后台地址
        $tmp['=========后台入口:'] = Env::getInstance()->getConfig('admin');
        $tmp['=========REST API后台入口:'] = Env::getInstance()->getConfig('rest_admin');
        return ['data' => $tmp, 'hasErr' => $hasErr, 'msg' => '-------  环境命令初始化...  -------'];
    }
}