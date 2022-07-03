<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Helper;

use Weline\Backend\Model\BackendUser;
use Weline\Framework\Http\Request;

class Data extends \Weline\Framework\App\Helper
{
    protected Request $_request;
    private BackendUser $adminUser;

    public function __construct(
        Request   $_request,
        BackendUser $adminUser
    ) {
        $this->_request = $_request;
        $this->adminUser = $adminUser;
    }

    /**
     * @DESC          # 返回管理员
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/11/9 14:06
     * 参数区：
     * @return BackendUser
     */
    public function getRequestBackendUser(): BackendUser
    {
        $username = $this->_request->getParam('username');
        try {
            return $this->adminUser->load('username', $username);
        } catch (\Exception $exception) {
            return $this->adminUser;
        }
    }
}
