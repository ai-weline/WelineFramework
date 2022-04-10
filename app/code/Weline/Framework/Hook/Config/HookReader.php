<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Hook\Config;

use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Hook\Cache\HookCacheFactory;
use Weline\Framework\System\File\Scanner;
use Weline\Framework\System\ModuleFileReader;

class HookReader extends ModuleFileReader
{
    private CacheInterface $hookCache;
    protected string $path = 'hooks';

    public function __construct(HookCacheFactory $cacheFactory, Scanner $scanner, string $path = 'view'. DIRECTORY_SEPARATOR .'hooks')
    {
        $this->hookCache = $cacheFactory->create();
        parent::__construct($scanner, $path);
    }

    public function getFileList(\Closure $callback = null): array
    {
        $cache_key = 'hooks::'.$this->getPath();
        if (PROD && $data = $this->hookCache->get($cache_key)) {
            return $data;
        }
        if (empty($callback)) {
            $callback = function ($modules_files) {
                $modules_files_ = [];
                foreach ($modules_files as $modules_file) {
                    foreach ($modules_file as $module=>$module_file) {
                        if ($module_file) {
                            $hooker_file_arr = explode('view', $module_file);
                            $modules_files_[$module] = $module.'::'.array_pop($hooker_file_arr);
                        }
                    }
                }
                return $modules_files_;
            };
        }
        $data = parent::getFileList($callback);
        $this->hookCache->set($cache_key, $data);
        return $data;
    }

    public function setPath(string $path)
    {
        return parent::setPath('view'. DIRECTORY_SEPARATOR .'hooks' . DIRECTORY_SEPARATOR . $path.'.phtml');
    }
}
