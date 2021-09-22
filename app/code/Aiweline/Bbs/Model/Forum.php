<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Bbs\Model;

use Weline\Framework\Database\Model;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Forum extends Model
{
    const url = 'url';

    function __construct(
        array $data = []
    )
    {
        parent::__construct($data);
    }

    function provideTable(): string
    {
        return '';
    }

    function providePrimaryField(): string
    {
        return '';
    }

    function setup(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement setup() method.
    }

    function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    function install(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement install() method.
    }

    private function getRequest(): Request
    {
        return ObjectManager::getInstance(Request::class);
    }

    function set_data_before(string|array $key, mixed $value = null)
    {
        if ($this::fetch_data === $key) {
            foreach ($value as &$item) {
                $item->setUrl($this->getRequest()->getUrl("forum-{$item['fid']}.htm"));
            }
        }
    }

    /**
     * @DESC          # 获取Url
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/22 19:35
     * 参数区：
     */
    function getUrl()
    {
        return $this->getData('url');
    }

    /**
     * @DESC          # 设置URL
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/22 19:35
     * 参数区：
     * @param string $path
     * @return Forum
     */
    function setUrl(string $path): Forum
    {
        return $this->setData(self::url, $this->getRequest()->getUrl($path));
    }
}
