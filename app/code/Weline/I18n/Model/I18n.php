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

class I18n extends \Weline\Framework\Database\Model
{
    /**
     * @var Reader
     */
    private Reader $reader;

    /**
     * I18n 初始函数...
     * @param Reader $reader
     * @param array $data
     */
    public function __construct(
        Reader $reader,
        array $data = []
    )
    {
        parent::__construct($data);
        $this->reader = $reader;
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
                    $locals[$index . '-' . __($name)] = $locale;
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
                        $handle = fopen($i18n_file->getOrigin(), 'r');
                        $is_utf8 = false;
                        while (($data = fgetcsv($handle)) !== false) {
                            // 下面这行代码可以解决中文字符乱码问题
//                             $data[0] = iconv('gbk', 'utf-8', $data[0]);
//                             $data[1] = iconv('gbk', 'utf-8', $data[1]);
                            if (!$is_utf8) {
                                if (md5(mb_convert_encoding($data[0], 'utf-8', 'utf-8')) === md5($data[0])) {
                                    $is_utf8 = true;
                                } else {
                                    throw new Exception(__('i18n翻译文件仅支持utf-8编码：%1', [$local_filename]));
                                }
                            }
                            $locals_words[$local_filename][$data[0]] = $data[1];
                        }

                        fclose($handle);
                    }
                }
            }
        }
        $this->convertToLanguageFile($locals_words);
        return $locals_words;
    }

    /**
     * @DESC         |默认汉语
     *
     * 参数区：
     *
     * @param string $local_code
     * @return array|mixed
     * @throws Exception
     */
    function getLocalWords(string $local_code = 'zh_Hans_CN')
    {
        $words = [];
        if (isset($this->getLocalsWords()[$local_code])) {
            $words = $this->getLocalsWords()[$local_code];
        } else if (isset($this->getLocalsWords()['zh_Hans_CN'])) {
            $words = $this->getLocalsWords()['zh_Hans_CN'];
        }
        return $words;
    }

    /**
     * @DESC         |将翻译词组写入翻译文件
     *
     * 参数区：
     *
     * @param array $locals_words
     */
    function convertToLanguageFile(array $locals_words)
    {
        foreach ($locals_words as $local => $locals_word) {
            $words_filename = Env::path_TRANSLATE_FILES_PATH . $local . '.php';
            $file = new \Weline\Framework\System\File\Io\File();
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
