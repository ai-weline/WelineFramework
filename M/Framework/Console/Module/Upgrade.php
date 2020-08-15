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


use M\Framework\App;
use M\Framework\App\Env;
use M\Framework\Console\CommandAbstract;
use M\Framework\FileSystem\App\Scanner as AppScanner;
use M\Framework\Module\Helper\Data;

class Upgrade extends CommandAbstract
{

    /**
     * @DESC         |方法描述
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param array $args
     * @return mixed|void
     */
    public function execute($args = array())
    {
        // 删除路由文件
        foreach (Env::router_files_PATH as $path) {
            if (is_file($path)) exec(App::helper()->getConversionCommand('rm -f') . $path);
        }
        // 扫描代码
        $scanner = new AppScanner();
        $apps = $scanner->scanAppModules();

        $this->printer->note('模块更新...');
        // 注册模块
        $all_modules = [];
        foreach ($apps as $vendor => $modules) {
            foreach ($modules as $name => $register) {
                $all_modules[$vendor . '_' . $name] = $register;
                require APP_PATH . $register;
            }
        }
        // 更新模块
        $module_list = Env::getInstance()->getModuleList();
        if (empty($module_list)) {
            $this->printer->error('请先更新模块:bin/m module:upgrade');
            exit();
        }
        $module_list = array_intersect_key($module_list, $all_modules);

        $helper = new Data();
        $helper->updateModules($module_list);

        $this->printer->note('模块更新完毕！');
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '升级模块';
    }

    /**
     * ----------辅助函数--------------
     */
}