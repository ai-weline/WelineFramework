<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller\Config;

use Weline\Admin\Model\AdminUserConfig;
use Weline\Admin\Session\AdminSession;

class Set extends \Weline\Admin\Controller\BaseController
{
    private AdminUserConfig $adminUserConfig;
    private AdminSession $adminSession;

    function __construct(
        AdminUserConfig $adminUserConfig,
        AdminSession    $adminSession
    )
    {

        $this->adminUserConfig = $adminUserConfig;
        $this->adminSession    = $adminSession;
    }

    function postIndex()
    {
        $data = json_decode($this->_request->getBodyParams(), true);
        try {
            $this->adminUserConfig->setAdminUserId($this->adminSession->getLoginUserID())
                                  ->addConfig('theme_model', $data['model'])
                                  ->forceCheck()
                                  ->save();
            $this->adminSession->setData('theme_model', $data['model']);
            return json_encode($this->success());
        } catch (\Exception $exception) {
            return json_encode($this->exception($exception));
        }
    }
}