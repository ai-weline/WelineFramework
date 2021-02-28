<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

/**
 *  * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/11/30
 * 时间：20:47
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 * @DESC         |获取字符串之间的内容
 *
 * 参数区：
 *
 * @param string $str
 * @param $startDelimiter
 * @param $endDelimiter
 * @return array
 */
function getStringBetweenContents(string $str, string $startDelimiter, string $endDelimiter): array
{
    $contents             = [];
    $startDelimiterLength = strlen($startDelimiter);
    $endDelimiterLength   = strlen($endDelimiter);
    $startFrom            = $contentStart            = $contentEnd            = 0;
    while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
        $contentStart += $startDelimiterLength;
        $contentEnd = strpos($str, $endDelimiter, $contentStart);
        if (false === $contentEnd) {
            break;
        }
        $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
        $startFrom  = $contentEnd + $endDelimiterLength;
    }

    return $contents;
}

/**
 * @DESC         |翻译
 *
 * 参数区：
 *
 * @param string $words
 * @param array $args
 * @throws \Weline\Framework\App\Exception
 * @return string
 */
function __(string $words, array $args=[])
{
    $filename = \Weline\Framework\App\Env::path_TRANSLATE_WORDS_FILE;
    if (! file_exists($filename)) {
        touch($filename);
    }

    try {
        $all_words = (array)include $filename;
    } catch (\Weline\Framework\App\Exception $exception) {
        throw new \Weline\Framework\App\Exception($exception->getMessage());
    }
    if ($args) {
        foreach ($args as $key=>$arg) {
            $words = str_replace('%' . (is_integer($key) ? $key + 1 : $key), $arg, $words);
        }
    }
    $all_words[] = $words;
    $file        = fopen($filename, 'w+');
    $text        = '<?php return ' . var_export($all_words, true) . ';';
    fwrite($file, $text);
    fclose($file);

    return $words;
}

/**
 * @DESC         |以大写字母分割字符串
 *
 * 参数区：
 *
 * @param string $str
 * @return array|false|string[]
 */
function m_split_by_capital(string $str)
{
    return preg_split('/(?=[A-Z])/', $str);
}
