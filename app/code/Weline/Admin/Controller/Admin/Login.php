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
        /**@var AdminUser $adminUser*/
        $adminUser = $this->adminUser->where('username', $username)
            ->where('password', $password)->find()->fetch();
        if($adminUser->getId()){
            $this->_session->login($adminUser->getData());
        }else{
            $this->_redirect;# TODO url跳转函数添加
        }
        $this->getSession()->login();
    }
}