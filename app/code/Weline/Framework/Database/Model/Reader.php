<?php
/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Model;


use Weline\Framework\Database\AbstractModel;
use Weline\Framework\Database\ModelInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Setup\Db\ModelSetup;
use Weline\Framework\Setup\Db\Setup;
use Weline\Framework\System\File\Data\File;
use Weline\Framework\System\File\Scanner;

class Reader extends \Weline\Framework\System\ModuleFileReader
{
    function __construct(Scanner $scanner, $path = 'Model' . DIRECTORY_SEPARATOR)
    {
        parent::__construct($scanner, $path);
    }

    /**
     * @DESC          # 方法描述
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/6 21:34
     * 参数区：
     * @return array
     */
    public function read(): array
    {
        $callback = function ($files) {
            foreach ($files as $vendor => $vendor_modules) {
                foreach ($vendor_modules as $module => $models) {
                    foreach ($models as $model_path => $model_files) {
                        /**@var File $model_file */
                        foreach ($model_files as $key=>$model_file) {
                            $model = new \ReflectionClass($model_file->getNamespace() . '\\' . $model_file->getFilename());
                            if (!$model->getParentClass() || ($model->getParentClass()->getName() !== \Weline\Framework\Database\Model::class && $model->getParentClass()->getName() !== \Weline\Framework\Database\AbstractModel::class)) {
                                unset($model_files[$key]);
                            }
                        }
                        if(empty($model_files)){
                            unset($models[$model_path]);
                        }else{
                            $models[$model_path] = $model_files;
                        }
                    }
                    if(empty($models)){
                        unset($vendor_modules[$module]);
                    }else{
                        $vendor_modules[$module] = $models;
                    }
                }
                if(empty($vendor_modules)){
                    unset($files[$vendor]);
                }else{
                    $files[$vendor] = $vendor_modules;
                }
            }
            return $files;
        };
        return $this->getFileList($callback);
    }

    public function update(){
        /**@var ModelSetup $modelSetup*/
        $modelSetup = ObjectManager::getInstance(ModelSetup::class);
        foreach ($this->read() as $vendor=>$vendor_modules) {
            foreach ($vendor_modules as $module=>$models){
                foreach ($models as $model_path=>$model_files) {
                    /**@var File $model_file*/
                    foreach ($model_files as $model_file) {
                        /**@var AbstractModel $model*/
                        $model = ObjectManager::getInstance($model_file->getNamespace().'\\'.$model_file->getFilename());
                        $modelSetup->putModel($model);
                        $model->setup($modelSetup);
                    }
                }
            }
        }
    }
}