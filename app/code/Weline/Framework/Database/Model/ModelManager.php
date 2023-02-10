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
use Weline\Framework\Database\Model;
use Weline\Framework\Database\ModelInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Module\Config\ModuleFileReader;
use Weline\Framework\Module\Model\Module;
use Weline\Framework\Output\Printing;
use Weline\Framework\Register\Register;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;
use Weline\Framework\System\File\Data\File;

class ModelManager
{
    private ModuleFileReader $moduleReader;
    private Printing $printing;

    public function __construct(
        ModuleFileReader $moduleReader,
        Printing         $printing
    )
    {
        $this->moduleReader = $moduleReader;
        $this->printing     = $printing;
    }


    public function update(Module $module, Context $context, string $type)
    {
        if (!in_array($type, ['setup', 'upgrade', 'install'])) {
            throw new Exception(__('$type允许的值不在：%1 中', "'setup','upgrade','install'"));
        }
        $modelSetup       = ObjectManager::getInstance(ModelSetup::class);
        $model_files_data = array_reverse($this->moduleReader->readClass($module->getBasePath(), 'Model'));
        foreach ($model_files_data as $key => $model_class) {
            if (PROD) {
                $this->printing->printing($model_class);
            }
            if (class_exists($model_class)) {
                $model = ObjectManager::getInstance($model_class);
                if ($model instanceof AbstractModel) {
                    if (PROD) {
                        $this->printing->printing($model::class);
                    }
                    $modelSetup->putModel($model);
                    # 执行模型升级
                    $model->$type($modelSetup, $context);
                }
            }
        }
    }
}
