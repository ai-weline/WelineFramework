<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/10
 * 时间：22:28
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Console\Module;


use M\Framework\App\Env;
use M\Framework\Console\CommandAbstract;
use M\Framework\Console\ConsoleException;
use M\Framework\Module\Handle;
use M\Framework\Module\Helper\Data;


class Remove extends CommandAbstract
{

    /**
     * @DESC         |执行方法
     *
     * 参数区：
     *
     * @param array $args
     * @return mixed|void
     * @throws ConsoleException
     * @throws \M\Framework\App\Exception
     */
    public function execute($args = array())
    {
        array_shift($args);
        if (empty($args)) throw new ConsoleException('缺少模块名参数。示例：module:remove Aiweline_demo Aiweline_Test');
        $this->printer->error(__("提示：此命令将执行以下模块的卸载程序。"));
        foreach ($args as $module) {
            $this->printer->warning($module);
        }
        $this->printer->error(__("是否继续（y/n）？"));
        $fp = fopen('/dev/stdin', 'r');
        $input = fgets($fp, 255);
        fclose($fp);

        if (strtolower(chop($input)) == 'y') {

            // 获得模块列表
            $module_list = Env::getInstance()->getModuleList();
            foreach ($args as $module) {
                $this->printer->note(__('执行 ') . $module . __(' 卸载程序...'));
                if (isset($module_list[$module])) {
                    $handle = new Handle();
                    $handle->remove($module);
                    // 卸载数组中模块
                    unset($module_list[$module]);
                } else {
                    $this->printer->warning($module . __(" 模块卸载失败：模块不存在！"));
                }

            }
            // 更新模块数据
            (new Data())->updateModules($module_list);
            (new Upgrade())->execute();

        } else {
            $this->printer->warning(__("已取消执行！"));
        }
    }

    /**
     * @DESC         |命令提示
     *
     * 参数区：
     *
     * @return string
     */
    public function getTip(): string
    {
        return "移除模块以及模块数据！并执行卸载脚本（如果有）";
    }
}