<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Observer;

use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\Event;
use Weline\Framework\Http\Url;

class BackendWhitelistUrl implements \Weline\Framework\Event\ObserverInterface
{
    private Url $url;

    public function __construct(
        Url $url
    ) {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        /**@var DataObject $data*/
        $data = $event->getData('data');
        $whitelist = $data->getData('whitelist_url');
        $data->setData('whitelist_url', array_merge($whitelist, [
            $this->url->build('/admin/login/post'),
            $this->url->build('/admin/login/verificationCode'),
            $this->url->build('/admin/login/verificationcode'),
            $this->url->build('/admin/login/index'),
            $this->url->build('/admin/login'),
        ]));
    }
}
