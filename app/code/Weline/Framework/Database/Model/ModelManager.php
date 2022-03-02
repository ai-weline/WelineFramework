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
use Weline\Framework\Register\Register;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;
use Weline\Framework\System\File\Data\File;

class ModelManager
{
    private Reader $moduleReader;
    private Printing $printing;

    function __construct(
        Reader   $moduleReader,
        Printing $printing
    )
    {
        $this->moduleReader = $moduleReader;
        $this->printing = $printing;
    }


    function update(string $module_name, Context $context, string $type)
    {
        if (!in_array($type, ['setup', 'upgrade', 'install'])) {
            throw new Exception(__('$type允许的值不在：%1 中', "'setup','upgrade','install'"));
        }
        foreach ($this->moduleReader->read() as $vendor => $vendor_modules) {
            foreach ($vendor_modules as $module => $model_files_data) {
                if ($module === $module_name) {
                    /**@var ModelSetup $modelSetup */
                    $modelSetup = ObjectManager::getInstance(ModelSetup::class);
                    foreach ($model_files_data as $model_path => $model_files) {
                        /**@var File $model_file */
                        foreach ($model_files as $model_file) {
                            /**@var ModelInterface|AbstractModel $model */
                            $model = ObjectManager::getInstance($model_file->getNamespace() . '\\' . $model_file->getFilename());
                            $modelSetup->putModel($model);
                            # 执行模型升级
                            $model->$type($modelSetup, $context);
                        }
                    }
                }
            }
        }
    }
}