<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\App;

use Weline\Framework\Http\Request;

class State
{
    const area_backend = 'backend';

    const area_frontend = 'frontend';

    const area_base = 'base';

    /**
     * @var Request
     */
    private Request $request;

    /**
     * State 初始函数...
     * @param Request $request
     */
    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }

    public function getStateCode()
    {
        p($this->request->getModuleName());

        return $this->request->getAreaRouter();
    }
}
