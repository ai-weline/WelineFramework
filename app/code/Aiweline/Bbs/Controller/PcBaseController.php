<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Bbs\Controller;

use Aiweline\Bbs\Model\Forum;
use Weline\Framework\Controller\PcController;

class PcBaseController extends PcController
{
    /**
     * @var Forum
     */
    private Forum $forum;

    public function __construct(
        Forum $forum
    ) {
        $this->assign('forum', $forum->select());
    }
}
