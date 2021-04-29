<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Phrase;

use Weline\Framework\App\Env;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Manager\ObjectManager;

class Parser
{
    protected static array $words = [];

    /**
     * @DESC         |翻译解析函数
     * DEV环境下解析字词并收集到generated/language/words.php
     *
     * 参数区：
     *
     * @param string $words
     * @param array $args
     * @throws \Weline\Framework\App\Exception
     */
    public static function parse(string $words, array $args)
    {
        $words = self::processWords($words);
        if ($args) {
            foreach ($args as $key => $arg) {
                $words = str_replace('%' . (is_integer($key) ? $key + 1 : $key), $arg, $words);
            }
        }

        return $words;
    }

    protected static function processWords(string $words)
    {
        // 仅加载一次翻译到对象self::$words
        if (empty(self::$words)) {
            // 先访问缓存
            /**@var \Weline\Framework\Cache\CacheInterface $phraseCache */
            $phraseCache = ObjectManager::getInstance(\Weline\Framework\Phrase\Cache\PhraseCache::class.'Factory');
            $phraseCache = ObjectManager::getInstance('\Weline\Framework\Phrase\Cache\PhraseCacheFactory');
            p($phraseCache);
            if(!DEV&&$phrase_words = $phraseCache->get())
            /**@var \Weline\Framework\Event\EventsManager $eventsManager */
            $eventsManager = ObjectManager::getInstance(\Weline\Framework\Event\EventsManager::class);
            $file_data     = new DataObject(['file_path'=>Env::path_TRANSLATE_DEFAULT_FILE]);
            $eventsManager->dispatch('Weline_Framework_phrase::get_words_file', ['file_data'=>$file_data]);
            $words_file = $file_data->getData('file_path');
            if (is_file($words_file)) {
                try {
                    /** @noinspection PhpIncludeInspection */
                    self::$words = (array)include $words_file;
                } catch (\Weline\Framework\App\Exception $exception) {
                    throw new \Weline\Framework\App\Exception($exception->getMessage());
                }
            }
        }
        // 如果有就替换
        if (isset(self::$words[$words])) {
            $words = self::$words[$words];
        }

        return $words;
    }
}
