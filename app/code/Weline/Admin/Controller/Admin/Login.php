<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller\Admin;

use Weline\Admin\Model\AdminUser;

class Login extends \Weline\Framework\App\Controller\BackendController
{
    protected AdminUser $adminUser;

    function __construct(
        AdminUser $adminUser
    )
    {
        $this->adminUser = $adminUser;
    }

    function post()
    {
        # 验证 form 表单
        if (empty($this->getSession()->getData('form_key'))) {
            $this->noRouter();
        }
        $username = $this->_request->getParam('username');
        $password = $this->_request->getParam('password');
        # FIXME 用户名检测登录次数 设置验证码
        if()
            /**@var AdminUser $adminUser*/
        $adminUser = $this->adminUser->where('username', $username)
            ->where('password', $password)->find()->fetch();
        if($adminUser->getId()){
            $this->_session->login($adminUser->getData());
            $this->getSession()->login();
        }else{
            $msg = __('账户不存在！');
            if($this->getSession()->getData('backend_login_times')>6){
                $msg = __('多次尝试失败！请联系管理员进行账户解锁！');
                # FIXME 记录登录次数到数据库
            }
            $this->getSession()->setData('backend_login_times', intval($this->getSession()->getData('backend_login_times'))+1);
            $this->redirect($this->getUrl('?error='.$msg));
        }
    }
}