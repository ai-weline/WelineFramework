<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Console\Deploy\StaticContent;

use Weline\Framework\Output\Cli\Printing;

class Set implements \Weline\Framework\Console\CommandInterface
{

    private Printing $printing;

    function __construct(
        Printing $printing
    ){

        $this->printing = $printing;
    }

    /**
     * @inheritDoc
     */
    public function execute($args = [])
    {

        if (isset($args[1]) && $type = $args[1]) {
            
        }else{
            $this->printing->error(__(''));
        }
    }

    function getTypes()
    {
        return [
            'static_file_rand_version' => '设置静态文件是否末尾添加随机字符，以保证浏览器不缓存文件而得到实时响应修改。'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getTip(): string
    {
        return '设置静态文件状态';
    }
}