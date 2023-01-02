<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/12/29 19:45:44
 */

namespace Weline\I18n\Observer;

use Weline\Framework\Event\Event;
use Weline\Framework\Phrase\Parser;
use Weline\I18n\Cache\I18nCache;

class ParserWordsRegister implements \Weline\Framework\Event\ObserverInterface
{
    public const WORDS_CACHE_KEY          = 'WELINE_FRAMEWORK_SYSTEM_WORDS_CACHE_KEY';
    public const FRONTEND_WORDS_CACHE_KEY = 'WELINE_FRAMEWORK_SYSTEM_WORDS_CACHE_KEY_FRONTEND';
    public const BACKEND_WORDS_CACHE_KEY  = 'WELINE_FRAMEWORK_SYSTEM_WORDS_CACHE_KEY_BACKEND';
    private \Weline\Framework\Cache\CacheInterface $cache;

    public function __construct(
        I18nCache $cache
    )
    {
        $this->cache = $cache->create();
    }

    /**
     * @inheritDoc
     */
    public function execute(Event $event)
    {
        $this->cache->set(self::WORDS_CACHE_KEY, Parser::getWords());
        // 存储到基础文件中
    }

    public function getWords(): array
    {
        $words = $this->cache->get(self::WORDS_CACHE_KEY);
        if (!is_array($words)) {
            $words = [];
        }
        return $words;
    }
}
