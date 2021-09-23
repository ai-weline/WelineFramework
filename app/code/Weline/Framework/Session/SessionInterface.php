<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Session;

interface SessionInterface
{
    public function open();

    public function set($name, $value);

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param $name
     * @return mixed
     */
    public function get($name): mixed;

    public function del($name);

    public function des();

    public function gc(int $sessMaxLifeTime);
}
