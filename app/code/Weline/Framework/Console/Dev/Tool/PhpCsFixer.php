<?php
declare(strict_types=1);
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(Aiweline)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2021/5/7
 * 时间：20:24
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Weline\Framework\Console\Dev\Tool;


class PhpCsFixer implements \Weline\Framework\Console\CommandInterface
{

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        // 运行代码标准程序 php-cs-fixer
        if (PHP_CS && is_file(\Weline\Framework\App\Env::extend_dir . 'php-cs-fixer-v2.phar')) {
            exec('php ' .\Weline\Framework\App\Env::extend_dir . 'php-cs-fixer-v2.phar fix', $out);
        } else {
            if (DEV) new \Weline\Framework\App\Exception('标准化代码文件缺失：' . \Weline\Framework\App\Env::extend_dir . 'php-cs-fixer-v2.phar');
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