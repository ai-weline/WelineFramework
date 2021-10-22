<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Bbs\Model;

use Aiweline\Bbs\Cache\BbsCache;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Thread extends \Weline\Framework\Database\Model
{

    private CacheInterface $bbsCache;

    function __construct(
        BbsCache $bbsCache,
        array    $data = []
    )
    {
        parent::__construct($data);
        $this->bbsCache = $bbsCache->create();
    }

    const table = 'bbs_thread';
    const fields_ID = 'tid';

    function provideTable(): string
    {
        return self::table;
    }

    public function providePrimaryField(): string
    {
        return self::fields_ID;
    }


    /**
     * @inheritDoc
     */
    function setup(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement setup() method.
    }

    /**
     * @inheritDoc
     */
    function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    function install(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement install() method.
    }

    function fetch_after()
    {
        $cache_key = 'bbs_thread_cache';
        if ($threads = $this->bbsCache->get($cache_key)) {
            return $threads;
        }
        $threads = $this->getData('query_data');
        if ($threads) {
            /**@var Tag $tagModel */
            $tagModel = ObjectManager::getInstance(Tag::class);
            /**@var Forum $forumModel */
            $forumModel = ObjectManager::getInstance(Forum::class);
            foreach ($threads as $key => &$thread) {
                // 读取标签tag
                $tagids = $thread->getData('tagids');
                $tags = $tagModel->where("tagid", $tagids, 'in')->select()->fetch();
                $thread->setData('tags', $tags);
                // 读取主题名
                $fid = $thread->getData('fid');
                $forum = $forumModel->where("fid", $fid)->select()->fetch();
                $thread->setData('forum', $forum);
            }
        }
        $this->bbsCache->set($cache_key, $threads, 3600);
        return $threads;
    }
}