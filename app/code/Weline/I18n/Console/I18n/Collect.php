<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\I18n\Console\I18n;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Weline\Framework\App\Env;
use Weline\Framework\Output\Cli\Printing;

class Collect implements \Weline\Framework\Console\CommandInterface
{
    private Client $client;
    private Printing $printing;

    public function __construct(Client $client, Printing $printing)
    {
        $this->client   = $client;
        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute(array $args = [])
    {
        # 设置语言翻译收集配置

        # 查找所有已激活模块的模板文件，进行模板生成
    }


    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '收集翻译词';
    }
}
