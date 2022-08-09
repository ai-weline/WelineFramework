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
use Weline\I18n\Cache\I18nCacheFactory;
use Weline\I18n\Parser;
use Weline\Framework\System\File\Scanner;
use Weline\Framework\System\ModuleFileReader;

class Reader extends ModuleFileReader
{
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var CacheInterface
     */
    protected CacheInterface $i18nCache;

    /**
     * @var Scanner
     */
    protected Scanner $scanner;

    /**
     * @var Parser
     */
    protected Parser $parser;

    /**
     * Read 初始函数...
     *
     * @param Scanner          $scanner
     * @param Request          $request
     * @param I18nCacheFactory $cache
     * @param Parser           $parser
     */
    public function __construct(
        Scanner          $scanner,
        Request          $request,
        I18nCacheFactory $cache,
        Parser           $parser
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
        $lang_packs       = $lang_pack_reader->getLanguagePack();
        $cache_key = 'cache_i18n_lang_packs';
        if ($data = $this->i18nCache->get($cache_key)) {
            return $data;
        }
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
//                                file_put_contents(__DIR__ . 'test.txt', var_export($vendor_module_i18ns, true));
//                        die;
        $data = array_merge($lang_packs, $vendor_module_i18ns);
        $this->i18nCache->set($cache_key, $data);
        return $data;
    }
}
