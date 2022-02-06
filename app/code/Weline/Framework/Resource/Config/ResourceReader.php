<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Resource\Config;

use Weline\Framework\System\File\Data\File;

abstract class ResourceReader extends \Weline\Framework\Module\File\Reader implements ResourceReaderInterface
{
    public string $file;
    public string $path;
    public string $source_type;

    function __construct(string $path, string $file, $source_type, array $data = [])
    {
        $this->file = $file;
        $this->path = $path;
        $this->source_type = $source_type;
        parent::__construct($path, $data);
    }

    function getSourceType(): string
    {
        return $this->source_type;
    }

    function getFileList(\Closure $callback = null): array
    {
        if (empty($callback)) $callback = function ($data) {
            $need_data = [];
            foreach ($data as $vendor => $module_data) {
                foreach ($module_data as $name => $dir_data) {
                    foreach ($dir_data as $dir => $dir_files) {
                        /**@var File $dir_file */
                        foreach ($dir_files as $dir_file) {
                            if ($this->file === $dir_file->getBaseName() || empty($this->file)) {
                                $area = 'frontend';
                                if (is_int(strpos($dir_file->getNamespace(), 'backend'))) {
                                    $area = 'backend';
                                }
                                $need_data[] = [
                                    'module' => $vendor . '_' . $name,
                                    'dir' => $dir,
                                    'area' => $area,
                                    'file' => $dir_file->getRelate(),
                                    'origin' => $dir_file->getOrigin(),
                                ];
                            }
                        }
                    }
                }
            }
            return $need_data;
        };
        return parent::getFileList($callback);
    }
}