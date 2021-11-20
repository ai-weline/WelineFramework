<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Resource\Console\Resource;

use http\Env;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Resource\CompilerInterface;

class Compiler implements \Weline\Framework\Console\CommandInterface
{
    private Printing $printing;

    function __construct(
        Printing $printing
    )
    {

        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {
        /*$source_type = '';
        if (isset($args[1])) {
            $source_type = $args[1];
        }*/

        foreach ($this->getTypes() as $type) {
            $type = ucfirst($type);
            /**@var CompilerInterface $compiler*/
            $compiler = ObjectManager::getInstance("\Weline\Framework\Resource\Compiler\{$type}Compiler");
            // TODO 编译静态less
            foreach ( as $item) {

            }
            $compiler->compiler($less_file, $out_file);
        }


    }

    function getTypes()
    {
        return [
            'less' => __('编译less静态资源！')
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return __('编译资源');
    }
}