<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Session;

use Weline\Admin\Model\AdminUser;
use Weline\Framework\Database\AbstractModel;

class AdminSession extends \Weline\Framework\App\Session\BackendSession
{
    function loginUser(AdminUser $user): static
    {
        return parent::login($user->getUsername(), $user->getId());
    }

    function getLoginUser(string $model = AdminUser::class): ?AbstractModel
    {
        return parent::getLoginUser($model);
    }
}