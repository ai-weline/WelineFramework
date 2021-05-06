<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\I18n\Config;

use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\System\File\Data\File;
use Weline\I18n\Cache\I18nCache;
use Weline\I18n\Parser;
use Weline\Framework\System\File\Scanner;
use Weline\Framework\System\ModuleFileReader;

class Reader extends ModuleFileReader
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
     * @DESC         |读取模块i18n翻译文件
     *
     * 参数区：
     */
    public function getAllI18ns()
    {
        /**@var LanguagePackReader $lang_pack_reader */
        $lang_pack_reader = ObjectManager::getInstance(LanguagePackReader::class);
        $lang_packs = $lang_pack_reader->getLanguagePack();
        // 模块翻译
        $vendor_module_i18ns = [];
        foreach ($this->getFileList() as $vendor => $module_files) {
            foreach ($module_files as $module => $item) {
                /**@var $i File*/
                foreach ($item as $ims) {
                    foreach ($ims as $im) {
                        if ($im->getExtension() === 'csv') {
                            $vendor_module_i18ns[$vendor][$module][]=$im;
                        }
                    }
                }
            }
        }

        return array_merge($lang_packs,$vendor_module_i18ns);
    }
}
