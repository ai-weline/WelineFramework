<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Frontend\Session;

use Weline\Admin\Model\AdminUser;
use Weline\Framework\Database\AbstractModel;
use Weline\Frontend\Model\User;

class UserSession extends \Weline\Framework\App\Session\FrontendSession
{
    public function loginUser(AdminUser $user): static
    {
        return parent::login($user->getUsername(), $user->getId());
    }

    public function getLoginUser(string $model = User::class): ?AbstractModel
    {
        return parent::getLoginUser($model);
    }
}
