<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console\Resource\Compiler\RequireJs;

use Weline\Framework\App\Env;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Manager\ObjectManager;
use Weline\Theme\Console\Resource\CompilerInterface;
use Weline\Framework\View\Template;

class Compiler implements CompilerInterface
{

    protected \Weline\Theme\Console\Resource\Compiler\RequireJs\Reader $reader;

    function __construct(
        \Weline\Theme\Console\Resource\Compiler\RequireJs\Reader $reader
    )
    {
        $this->reader = $reader;
    }

    protected ?EventsManager $eventsManager = null;

    function getEventManager(): EventsManager
    {
        if (!isset($this->eventsManager, $_)) {
            $this->eventsManager = ObjectManager::getInstance(EventsManager::class);
        }
        return $this->eventsManager;
    }

    public function compile(string $source_file = null, string $out_file = null)
    {
        $config_resources = $this->reader->parserRequireConfigs();
        foreach ($config_resources as $area => $config_resource) {
            $this->getEventManager()->dispatch('Weline_Theme::compiler', ['data' => new DataObject(['area' => $area, 'type' => 'require.configs.js', 'resources' => $config_resource])]);
        }
    }

}