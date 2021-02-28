<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Index\Controller;

use Weline\Framework\App\State;

class Test extends \Weline\Framework\App\Controller\FrontendController
{
    /**
     * @var State
     */
    private State $state;

    /**
     * Test 初始函数...
     * @param State $state
     */
    public function __construct(
        State $state
    ) {
        $this->state = $state;
    }

    public function Dd()
    {
        P($this->state->getStateCode(), 1);
        p($this);
    }
}
