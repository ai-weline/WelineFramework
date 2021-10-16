<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Bbs\Controller;

use Weline\Framework\Controller\PcController;

class Index extends PcController
{

    private \Aiweline\Bbs\Model\Forum $forum;
    private \Aiweline\Bbs\Model\Thread $thread;

    function __construct(
        \Aiweline\Bbs\Model\Forum $forum,
        \Aiweline\Bbs\Model\Thread $thread,
    )
    {
        $this->forum = $forum;
        $this->thread = $thread;
    }

    public function index()
    {
        // 读取排行置顶的
        $this->assign('forum', $this->forum->select()->fetch());
        $this->assign('new_thread', $this->thread->order('`create_date`','DESC')->limit(10)->select()->fetch());
        $this->fetch();
    }

    public function test()
    {
        $this->assign('data', 123);
        $this->assign('title', 123);

        return $this->fetch();
    }
}
