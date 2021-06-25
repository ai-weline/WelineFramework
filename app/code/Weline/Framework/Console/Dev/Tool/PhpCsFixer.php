<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console\Dev\Tool;

use Weline\Framework\Output\Cli\Printing;

class PhpCsFixer implements \Weline\Framework\Console\CommandInterface
{
    private Printing $printing;

    function __construct(
        Printing $printing
    )
    {
        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        array_shift($args);
        $args = implode(' ', $args);
        // 运行代码标准程序 php-cs-fixer
        if (PHP_CS && is_file(\Weline\Framework\App\Env::extend_dir . 'php-cs-fixer-v3.0.phar')) {
            $this->printing->note(__('正在美化代码...'));
            exec('php ' . \Weline\Framework\App\Env::extend_dir . 'php-cs-fixer-v3.0.phar fix ' . $args, $out);
            $this->printing->success(__('代码美化完成...'));
        } else {
            if (DEV) {
                new \Weline\Framework\App\Exception(__('标准化代码文件缺失：%1',\Weline\Framework\App\Env::extend_dir . 'php-cs-fixer-v3.0.phar'));
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
