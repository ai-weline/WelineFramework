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
     * @return string
     * @throws \ReflectionException
     * @throws \Weline\Framework\Exception\Core
     */
    public function register($data, string $version = '', string $description = ''): string
    {
        // 参数检查
        if (!isset($data['name']) || !isset($data['path'])) {
            throw new ConsoleException('注册文件参数params必须包含：name和path。 样例：["name"=>"default主题"，"path"=>__DIR__]');
        }

        // 检查主题是否已经安装
        $this->welineTheme->load('name', $data['name']);
//        pp($data['name']);
        $action_string = __('安装');
        if ($this->welineTheme->getId()) {
            if ($this->welineTheme->getPath() !== $data['path'] . DIRECTORY_SEPARATOR) {
                $this->printing->setup($data['name'] . __(' 主题更新...'));
                $action_string = __('更新');
            } else {
                return '';
            }
        }
        // 处理主题路径
        $theme_path = str_replace(Env::path_CODE_DESIGN, '', $data['path']);
        // 开始主题注册 save 方法自带事务
        try {
            if ($this->welineTheme->getId()) {
                // 更新
                $this->welineTheme
                    ->setName($data['name'])
                    ->setPath($theme_path)
                    ->save();
            } else {
                // 新安装
                $this->welineTheme
                    ->setName($data['name'])
                    ->setIsActive(1)
                    ->setPath($theme_path)
                    ->save();
            }

            $this->printing->success($data['name'] . __(" 主题{$action_string}完成!"));
        } catch (\Exception $exception) {
            $this->printing->error($data['name'] . __(" 主题{$action_string}异常!"));
            $this->printing->success($exception->getMessage());
            throw  $exception;
        }

        return '';
    }
}
