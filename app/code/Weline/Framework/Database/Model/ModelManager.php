<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Database\Model;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Database\AbstractModel;
use Weline\Framework\Database\ModelInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Printing;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;
use Weline\Framework\System\File\Data\File;

class ModelManager
{
    private Reader $moduleReader;
    private Printing $printing;

    function __construct(
        Reader $moduleReader,
        Printing $printing
    )
    {
        $this->moduleReader = $moduleReader;
        $this->printing = $printing;
    }

    function update(string $module_name,Context $context,string $type){
        if(!in_array($type, ['setup','upgrade','install'])){
            throw new Exception(__('$type允许的值不在：%1 中',"'setup','upgrade','install'"));
        }
        $modules = Env::getInstance()->getModuleList();
        foreach ($this->moduleReader->read() as $vendor=>$vendor_modules) {
            # FIXME 内存开销大，以后优化
            foreach ($vendor_modules as $module=>$models){
                $module_module_name = $vendor.'_'.$module;
                # 检测模型模组是否与传入模型相同
                if($module_module_name ===$module_name){
                    if(isset($modules[$module_name])&&$module = $modules[$module_name]){
                        /**@var ModelSetup $modelSetup*/
                        $modelSetup = ObjectManager::getInstance(ModelSetup::class);
                        foreach ($models as $model_path=>$model_files) {
                            /**@var File $model_file*/
                            foreach ($model_files as $model_file) {
                                /**@var ModelInterface|AbstractModel $model*/
                                $model = ObjectManager::getInstance($model_file->getNamespace().'\\'.$model_file->getFilename());
                                $modelSetup->putModel($model);
                                # 执行模型升级
                                $model->$type($modelSetup,$context);
                            }
                        }
                    }else{
                        $this->printing->note(__('请安装 %1 后重试！',$module_name));
                        exit(0);
                    }
                }
            }
        }
    }
}