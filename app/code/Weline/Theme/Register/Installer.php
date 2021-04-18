<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Register;

use Weline\Framework\App\Env;
use Weline\Framework\Console\ConsoleException;
use Weline\Framework\Output\Cli\Printing;
use Weline\Framework\Register\RegisterInterface;
use Weline\Theme\Model\WelineTheme;

class Installer implements RegisterInterface
{
    /**
     * @var WelineTheme
     */
    private WelineTheme $welineTheme;
    /**
     * @var Printing
     */
    private Printing $printing;

    /**
     * Installer 初始函数...
     * @param WelineTheme $welineTheme
     * @param Printing $printing
     */
    public function __construct(
        WelineTheme $welineTheme,
        Printing $printing
    )
    {
        $this->welineTheme = $welineTheme;
        $this->printing = $printing;
    }

    /**
     * @DESC         |注册主题
     *
     * 参数区：
     *
     * @param $data
     * @param string $version
     * @param string $description
     */
    public function register($data, string $version = '', string $description = '')
    {
        // 参数检查
        if (!isset($data['name']) || !isset($data['path'])) {
            throw new ConsoleException('注册文件参数params必须包含：name和path。 样例：["name"=>"default主题"，"path"=>__DIR__]');
        }

        // 处理主题路径
        $theme_path = str_replace(Env::path_CODE_DESIGN, '', $data['path']);

        // 开始主题事务注册
        $this->welineTheme->startTrans();
        try {
            $this->welineTheme
                ->setName($data['name'])
                ->setIsActive(1)
                ->setPath($theme_path);
            $this->welineTheme->commit();
            $this->printing->success($data['name'] . __('主题安装完成!'));
        } catch (\Exception $exception) {
            $this->printing->success($data['name'] . __('主题安装异常!'));
            $this->welineTheme->rollback();
            throw $exception;
        }
    }
}
