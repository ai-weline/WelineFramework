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
    public function execute($args = [])
    {
        $frontend_routers = (array)(require Env::path_FRONTEND_PC_ROUTER_FILE);
        $this->printing->warning(__('前台词组收集：'));
        foreach ($frontend_routers as $frontend_router => $router_data) {
            $url = 'http://' . Env::getInstance()->getConfig('domain') . '/' . $frontend_router;
            $this->printing->note($url);
            $this->client->get($url);
        }
        $this->printing->warning(__('后台词组收集：'));
        $backend_routers = (array)(require Env::path_BACKEND_PC_ROUTER_FILE);
        foreach ($backend_routers as $backend_router => $router_data) {
            $url = 'http://' . Env::getInstance()->getConfig('domain') . '/' . Env::getInstance()->getConfig('admin') . '/' . $backend_router;
            $this->printing->note($url);
            try {
                $this->client->get($url);
            } catch (\Exception) {
            }
        }
        $this->printing->success('收集完成！');
    }


    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '收集翻译词';
    }
}
