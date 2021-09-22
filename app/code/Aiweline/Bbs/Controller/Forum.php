<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Bbs\Controller;

use Weline\Framework\App\Controller\FrontendController;

class Forum extends FrontendController
{
    private \Aiweline\Bbs\Model\Forum $forum;

    function __construct(
        \Aiweline\Bbs\Model\Forum $forum
    )
    {
        $this->forum = $forum;
    }

    function index(){
        $params = $this->getRequest()->getParams();
        if(isset($params['fid'])&&$fid = $params['fid']){
            $forum = $this->forum->load($fid);
        }
    }
}