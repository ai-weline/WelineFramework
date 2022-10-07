<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/9/23 19:52:23
 */

namespace Weline\UrlManager\Observer;

use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\Event;
use Weline\Framework\Http\Request;
use Weline\UrlManager\Model\UrlRewrite;

class RouterRewrite implements \Weline\Framework\Event\ObserverInterface
{

    private UrlRewrite $urlRewrite;

    function __construct(
        UrlRewrite $urlRewrite
    )
    {
        $this->urlRewrite = $urlRewrite;
    }

    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        # TODO 检测为何多次执行，并且报错USER_ID字段不存在问题
        /**@@var DataObject $data */
        $data    = $event->getData('data');
        $rewrite = $this->urlRewrite->load(UrlRewrite::fields_REWRITE, $data->getData('uri'));
        if ($rewrite->getId()) {
            # 读取原地址
            $origin_path = $rewrite->getData('path');
            $data->setData('uri', $origin_path);
        }
    }
}