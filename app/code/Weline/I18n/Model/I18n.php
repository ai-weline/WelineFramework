<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\I18n\Model;

use Symfony\Component\Intl\Locales;

class I18n extends \Weline\Framework\Database\Model
{
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
}
