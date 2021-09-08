<?php
/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Model;


use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Database\AbstractModel;
use Weline\Framework\Database\Cache\DbModelCache;
use Weline\Framework\Database\ModelInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Db\ModelSetup;
use Weline\Framework\Setup\Db\Setup;
use Weline\Framework\System\File\Data\File;
use Weline\Framework\System\File\Scanner;

class Reader extends \Weline\Framework\System\ModuleFileReader
{
    private CacheInterface $dbModelCache;
    private ?array $models;

    function __construct(
        DbModelCache $dbModelCache,
        Scanner      $scanner,
                     $path = 'Model' . DIRECTORY_SEPARATOR
    )
    {
        parent::__construct($scanner, $path);
        $this->dbModelCache = $dbModelCache->create();
        $this->scanner = $scanner;
        $this->path = $path;
    }

    /**
     * @DESC          # 读取模型 开发模式没有缓存，非开发模式读取缓存
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/6 21:34
     * 参数区：
     * @return array
     */
    public function read(): array
    {
        if (empty($this->models)) {
            $cache_key = 'db_models';
            # 非开发模式读取缓存
            if (!DEV && $models = $this->dbModelCache->get($cache_key)) {
                return $this->models = $models;
            }
            # 模型读取回调（排除非模型文件）
            $callback = function ($files) {
                foreach ($files as $vendor => $vendor_modules) {
                    foreach ($vendor_modules as $module => $models) {
                        foreach ($models as $model_path => $model_files) {
                            /**@var File $model_file */
                            foreach ($model_files as $key => $model_file) {
                                $model = new \ReflectionClass($model_file->getNamespace() . '\\' . $model_file->getFilename());
                                if (!$model->getParentClass() || ($model->getParentClass()->getName() !== \Weline\Framework\Database\Model::class)) {
                                    unset($model_files[$key]);
                                }
                            }
                            if (empty($model_files)) {
                                unset($models[$model_path]);
                            } else {
                                $models[$model_path] = $model_files;
                            }
                        }
                        if (empty($models)) {
                            unset($vendor_modules[$module]);
                        } else {
                            $vendor_modules[$module] = $models;
                        }
                    }
                    if (empty($vendor_modules)) {
                        unset($files[$vendor]);
                    } else {
                        $files[$vendor] = $vendor_modules;
                    }
                }
                return $files;
            };
            $this->models = $this->getFileList($callback);
            # 模型数据缓存
            if (!DEV) $this->dbModelCache->set($cache_key, $this->models);
        }

        return $this->models;
    }
}