<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\ModuleManager\Console\Common;

use Weline\Framework\App\Env;
use Weline\Theme\Console\Theme\AbstractConsole;

class Merge extends AbstractConsole implements \Weline\Framework\Console\CommandInterface
{
    /**
     * @inheritDoc
     */
    public function execute(array $args = [])
    {
        $active_modules = Env::getInstance()->getActiveModules();
        $common_files   = [];
        foreach ($active_modules as $key => $module) {
            $common_files = array_merge($common_files, glob($module['base_path'] . 'Common' . DS . '*.php'));
        }
        # 将通用内容写到generated目录
        $content = '';
        foreach ($common_files as $file) {
            $content .= '//'.$file.PHP_EOL.trim(trim(file_get_contents($file),'<?php'),'?>');
        }
        file_put_contents(Env::path_FUNCTIONS_FILE, '<?php'.PHP_EOL.$content);
        $this->printing->success(__('处理完成！'));
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return __('收集模块中的通用函数或者通用文件');
    }
}