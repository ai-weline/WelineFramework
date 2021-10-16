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
    const table = 'm_forum';
    const url = 'url';
    const fields_fid = 'fid';
    const fields_name = 'name';
    const fields_rank = 'rank';
    const fields_threads = 'threads';
    const fields_todayposts = 'todayposts';
    const fields_todaythreads = 'todaythreads';
    const fields_brief = 'brief';
    const fields_announcement = 'announcement';
    const fields_accesson = 'accesson';
    const fields_create_date = 'create_date';
    const fields_icon = 'icon';
    const fields_moduids = 'moduids';
    const fields_seo_title = 'seo_title';
    const fields_seo_keywords = 'seo_keywords';
    const fields_well_nav_display = 'well_nav_display';
    const fields_well_display = 'well_display';
    const fields_well_news = 'well_news';

    function __construct(
        array $data = []
    )
    {
        parent::__construct($data);
    }

    function provideTable(): string
    {
        return 'bbs_forum';
    }

    function providePrimaryField(): string
    {
        return 'fid';
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

    /**
     * @DESC          # 设置URL
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/22 20:37
     * 参数区：
     * @param string|array $key
     * @param mixed|null $value
     */
    function set_data_before(string|array $key, mixed $value = null)
    {
        if ($this::fetch_data === $key) {
            foreach ($value as &$item) {
                $item->setUrl("forum/?fid={$item['fid']}");
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

    function getFid()
    {
        return $this->getData(self::fields_fid);
    }

    function setFid(string|int $fid): Forum
    {
        return $this->setData(self::fields_fid, $fid);
    }

    function getName(){
        return $this->getData('name');
    }

    function getThreads($page = 1, $pageSize = 20, $order = 'create_date', $order_sort = 'DESC'): array
    {
        return $this->joinModel(Thread::class , 't', 'main_table.fid=t.fid AND t.fid=' . $this->getFid(),'LEFT')->page($page, $pageSize)->order('t.' . $order, $order_sort)->select()->fetch();
    }
}
