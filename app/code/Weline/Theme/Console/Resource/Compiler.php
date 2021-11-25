<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Console\Resource;

use http\Env;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Output\Cli\Printing;

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
        $source_type = '';
        array_shift($args);
        foreach ($args as $arg) {
            if (!in_array($arg, array_keys($this->getTypes()))) {
                $this->printing->error(__('不存在的编译资源类型：%1，支持的资源类型：%2', [$arg, $this->getTypes(true)]));
            }
        }
        if (empty($source_type)) {
            foreach ($this->getTypes() as $key=>$type) {
                $key= ucfirst($key);
                /**@var CompilerInterface $compiler */
                $compiler = ObjectManager::getInstance("\Weline\Theme\Console\Resource\Compiler\\$key");
                $compiler->compile();
            }
        } else {
            /**@var CompilerInterface $compiler */
            $compiler = ObjectManager::getInstance("\Weline\Theme\Console\Resource\Compiler\\$source_type");
            $compiler->compile();
        }
    }

    function getTypes(bool $to_string = false)
    {
        $data = [
            'less' => __('编译less静态资源！'),
            'requireJs' => __('静态文件：require.config.js编译文件！'),
        ];
        if ($to_string) {
            $data = implode(',', $data);
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return __('编译资源');
    }
}