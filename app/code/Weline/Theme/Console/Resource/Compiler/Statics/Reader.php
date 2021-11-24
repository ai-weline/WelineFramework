<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console\Resource\Compiler\Statics;

use Weline\Framework\System\File\Data\File;

class Reader extends \Weline\Framework\Module\File\Reader
{
    private string $file;

    function __init()
    {
        parent::__init();
        $this->path = 'view';
        $this->file = 'require.config.js';
    }

    function setFile(string $file)
    {
        $this->file = $file;
        return $this;
    }

    function getFileList(\Closure $callback = null): array
    {
        $callback = function ($data) {
            $need_data = [];
            foreach ($data as $vendor => $module_data) {
                foreach ($module_data as $name => $dir_data) {
                    foreach ($dir_data as $dir => $dir_files) {
                        /**@var File $dir_file*/
                        foreach ($dir_files as $dir_file) {
                            if ($this->file === $dir_file->getBaseName()) {
                                $area = 'frontend';
                                if(is_int(strpos($dir_file->getNamespace(), 'backend'))){
                                    $area = 'backend';
                                }
                                $need_data[] = [
                                    'module' => $vendor . '_' . $name,
                                    'dir' => $dir,
                                    'area'=>$area,
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