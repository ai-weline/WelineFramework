<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

/**
 *  * 文件信息
 * 作者：邹万才
 * 网名：秋枫雁飞(可以百度看看)
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
if (!function_exists('getStringBetweenContents')) {
    function getStringBetweenContents(string $str, string $startDelimiter, string $endDelimiter): array
    {
        $contents = [];
        $startDelimiterLength = strlen($startDelimiter);
        $endDelimiterLength = strlen($endDelimiter);
        $startFrom = $contentStart = $contentEnd = 0;
        while (false !== ($contentStart = strpos($str, $startDelimiter, $startFrom))) {
            $contentStart += $startDelimiterLength;
            $contentEnd = strpos($str, $endDelimiter, $contentStart);
            if (false === $contentEnd) {
                break;
            }
            $contents[] = substr($str, $contentStart, $contentEnd - $contentStart);
            $startFrom = $contentEnd + $endDelimiterLength;
        }

        return $contents;
    }
}
if (!function_exists('__')) {
    /**
     * @DESC          # 翻译
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 22:48
     * 参数区：
     * @param string $words
     * @param array|string|null $args
     * @return string
     */
    function __(string $words, array|string $args = null): string
    {
        return \Weline\Framework\Phrase\Parser::parse($words, $args);
    }
}
if (!function_exists('m_split_by_capital')) {
    /**
     * @DESC          | 以大写字母分割字符串
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/8/16 21:13
     * 参数区：
     * @param string $str
     * @return array|bool
     */
    function m_split_by_capital(string $str): array|bool
    {
        return preg_split('/(?=[A-Z])/', $str);
    }
}
