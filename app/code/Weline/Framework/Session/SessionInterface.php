<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Session;

use Weline\Framework\Database\AbstractModel;

interface SessionInterface
{
    function getData(string $name);

    function setData(string $name, mixed $value);

    function isLogin();

    function login(string $user, int $user_id);

    function getLoginUser(string $model): ?AbstractModel;

    function getLoginUsername();

    function getLoginUserID();

    function logout();

    function getOriginSession();

    function destroy();

    function delete(string $name);
}