<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console\Dev\Tool;

class PhpCsFixer implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        array_shift($args);
        $args = implode(' ', $args);
        // 运行代码标准程序 php-cs-fixer
        if (PHP_CS && is_file(\Weline\Framework\App\Env::extend_dir . 'php-cs-fixer-v2.phar')) {
            exec('php ' . \Weline\Framework\App\Env::extend_dir . 'php-cs-fixer-v2.phar fix ' . $args, $out);
        } else {
            if (DEV) {
                new \Weline\Framework\App\Exception('标准化代码文件缺失：' . \Weline\Framework\App\Env::extend_dir . 'php-cs-fixer-v2.phar');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '代码美化工具';
    }
}
