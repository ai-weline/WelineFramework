<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller;

use Weline\Framework\App\Controller\BackendController;

class Index extends BackendController
{
    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     */
    public function index()
    {
        // TODO 需要新增后台静态文件特殊路径，否则会读取前台的session 导致默写调用session异常 产生大量的无用调用，影响性能
        $this->assign('post_url',$this->getUrl('admin/login/post'));
        if ($this->getSession()->isLogin()) {
            $this->fetch();
        } else {
            $this->fetch('login/login_type2');
        }
    }

    public function test(): string
    {
        return '111111111';
    }
}
