<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\I18n\Model;

use Symfony\Component\Intl\Locales;
use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\System\File\Data\File;
use Weline\I18n\Config\Reader;

class I18n
{
    /**
     * @var Reader
     */
    private Reader $reader;

    /**
     * I18n 初始函数...
     *
     * @param Reader $reader
     * @param array  $data
     */
    public function __construct(
        Reader $reader
    )
    {
        $this->reader = $reader;
    }

    /**
     * @DESC          # 返回Local代码
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/6/24 23:01
     * 参数区：
     *
     * @param string $locale_code
     *
     * @return string
     */
    public function getLocalByCode(string $locale_code): string
    {
        $locales = Locales::getLocales();
        foreach ($locales as $locale) {
            $locale = strtolower($locale);
            if (strtolower($locale_code) === $locale) {
                return $locale;
            }
        }
        return 'zh_cn';
    }

    /**
     * @DESC         |获取当地码
     *
     * 参数区：
     *
     * @return string[]
     */
    public function getLocals()
    {
        $locals = [];
        foreach (Locales::getLocales() as $index => $locale) {
            foreach (Locales::getNames() as $local => $name) {
                if ($locale === $local) {
                    $locals[$index] = str_pad($local, 15, ' ') . $name;
                }
            }
        }

        return $locals;
    }

    /**
     * @DESC         |获取所有翻译
     *
     * 参数区：
     *
     * @return array
     * @throws Exception
     */
    public function getLocalsWords()
    {
        // 所有语言
        $locals_names = Locales::getNames();
        // 所有语言对应存在的翻译词
        $locals_words = [];
        // 模块翻译覆盖语言包翻译
        $all_i18ns = $this->reader->getAllI18ns();
        foreach ($all_i18ns as $vendor => $all_i18n) {
            foreach ($all_i18n as $module => $i18ns) {
                /**@var $i18n_file File */
                foreach ($i18ns as $i18n_file) {
                    $local_filename = $i18n_file->getFilename();
                    if (isset($locals_names[$local_filename])) {
                        $handle  = fopen($i18n_file->getOrigin(), 'r');
                        $is_utf8 = false;
                        $line    = 1;
                        while (($data = fgetcsv($handle)) !== false) {
                            if (!isset($data[0])) {
                                throw new Exception(PHP_EOL . 'i18n翻译文件格式错误：' . $i18n_file->getOrigin() . '错误行号：' . $line . '  错误消息：没有翻译原文' . PHP_EOL . '读取内容：' . PHP_EOL . var_export($data, true));
                            }
                            $data[0] = trim($data[0]);
                            if (!isset($data[1])) {
                                throw new Exception(PHP_EOL . 'i18n翻译文件格式错误：' . $i18n_file->getOrigin() . '错误行号：' . $line . '  错误消息：没有翻译内容' . PHP_EOL . '读取内容：' . PHP_EOL . var_export($data, true));
                            }
                            $data[1] = trim($data[1]);
                            if (!$is_utf8) {
                                if (md5(mb_convert_encoding($data[0], 'utf-8', 'utf-8')) === md5($data[0])) {
                                    $is_utf8 = true;
                                } else {
                                    throw new Exception('i18n翻译文件仅支持utf-8编码：' . $i18n_file->getOrigin());
                                }
                            }
                            $locals_words[$local_filename][$data[0]] = $data[1];
                            $line                                    += 1;
                        }

                        fclose($handle);
                    }
                }
            }
        }

        return $locals_words;
    }

    /**
     * @DESC         |默认汉语
     *
     * 参数区：
     *
     * @param string $local_code
     *
     * @return array|mixed
     * @throws Exception
     */
    public function getLocalWords(string $local_code = 'zh_Hans_CN')
    {
        $words = [];
        if (isset($this->getLocalsWords()[$local_code])) {
            $words = $this->getLocalsWords()[$local_code];
        } elseif (isset($this->getLocalsWords()['zh_Hans_CN'])) {
            $words = $this->getLocalsWords()['zh_Hans_CN'];
        }

        return $words;
    }

    /**
     * @DESC         |将翻译词组写入翻译文件
     *
     * 参数区：
     *
     * @throws Exception
     */
    public function convertToLanguageFile()
    {
        $locals_words = $this->getLocalsWords();
        foreach ($locals_words as $local => $locals_word) {
            $words_filename = Env::path_TRANSLATE_FILES_PATH . $local . '.php';
            $file           = new \Weline\Framework\System\File\Io\File();
            $file->open($words_filename, $file::mode_w);
            $text = '<?php return ' . var_export($locals_word, true) . ';?>';

            try {
                $file->write($text);
            } catch (Exception $e) {
                throw new Exception(__('错误：' . $e->getMessage()));
            }
            $file->close();
        }
    }
}
