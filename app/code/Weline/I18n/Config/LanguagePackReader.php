<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\I18n\Config;

use Weline\Framework\System\File\App\Scanner;
use Weline\Framework\System\File\Data\File;

class LanguagePackReader
{
    /**
     * @var Scanner
     */
    private Scanner $scanner;

    /**
     * LanguagePackReader 初始函数...
     * @param Scanner $scanner
     */
    public function __construct(Scanner $scanner)
    {
        $this->scanner = $scanner;
    }

    public function getLanguagePack()
    {
        // 所有语言包
        $all_lan_pack = [];
        // 扫描代码
        $registers = $this->scanner->scanAppModules();
        foreach ($registers as $vendor => $modules) {
            foreach ($modules as $name => $register) {
                // i18n国际化
                $lang_register_file = BP . 'app' . DIRECTORY_SEPARATOR . 'i18n' . DIRECTORY_SEPARATOR . $register;
                if (@is_file($lang_register_file)) {
                    // 初始化搜索结果
                    $this->scanner->init();
                    $lang_module_files = $this->scanner->scanDirTree(dirname($lang_register_file));
                    foreach ($lang_module_files as $module_files) {
                        if (is_array($module_files)) {
                            foreach ($module_files as $module_file) {
                                if ($module_file instanceof File && $module_file->getExtension() === 'csv') {
                                    $all_lan_pack[$vendor][$name][] = $module_file;
                                }
                            }
                        }
                    }
                }
            }
        }

        return  $all_lan_pack;
    }
}
