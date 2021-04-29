<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\I18n\Config;

use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Http\Request;
use Weline\Framework\I18n\Cache\I18nCache;
use Weline\Framework\I18n\Parser;
use Weline\Framework\System\File\Scanner;
use Weline\Framework\System\FileReader;

class Reader extends FileReader
{
    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var CacheInterface
     */
    private CacheInterface $i18nCache;

    /**
     * @var Scanner
     */
    private Scanner $scanner;

    /**
     * @var Parser
     */
    private Parser $parser;

    /**
     * Read 初始函数...
     * @param Scanner $scanner
     * @param Request $request
     * @param I18nCache $cache
     * @param Parser $parser
     */
    public function __construct(
        Scanner $scanner,
        Request $request,
        I18nCache $cache,
        Parser $parser
    ) {
        $this->request   = $request;
        $this->i18nCache = $cache->create();
        $this->scanner   = $scanner;
        $this->parser    = $parser;
        parent::__construct($scanner, 'i18n');
    }

    /**
     * @DESC         |读取语言
     *
     * 参数区：
     */
    public function getLanguage()
    {
        $language = $this->request->getHeader('lang');
        if (! $language) {
        }
    }

    /**
     * @DESC         |读取 TODO 读取模块i18n翻译文件
     *
     * 参数区：
     */
    public function getAllI18ns()
    {
//        $this->
    }
}
