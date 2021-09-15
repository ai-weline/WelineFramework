<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Observer;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\Event;
use Weline\Framework\Event\ObserverInterface;
use Weline\Framework\View\Template;
use Weline\Theme\Cache\ThemeCache;
use Weline\Theme\Model\WelineTheme;

class TemplateFetchFile implements ObserverInterface
{
    /**
     * @var WelineTheme
     */
    private WelineTheme $welineTheme;

    /**
     * @var CacheInterface
     */
    private CacheInterface $themeCache;

    /**
     * TemplateFetchBefore 初始函数...
     * @param WelineTheme $welineTheme
     * @param CacheInterface $themeCache
     */
    public function __construct(
        WelineTheme $welineTheme,
        ThemeCache $themeCache
    ) {
        $this->welineTheme = $welineTheme;
        $this->themeCache  = $themeCache->create();
    }

    public function execute(Event $event)
    {
        /**
         * @var $template Template
         */
        $template = $event->getData('object');
        /**
         * @var $fileData DataObject
         */
        $fileData = $event->getData('data');

        $module_file_path = $fileData->getData('filename');
        # 开始分析主题路径
        try {
            $theme = $this->welineTheme->getActiveTheme();
        } catch (\Exception $exception){
            throw  new Exception(__('主题异常：') . $exception->getMessage());
        }

        if (! isset($theme)) {
            $theme = $this->welineTheme->setData(Env::default_theme_DATA);
        }
        // 组织主题文件位置
        $theme_file_path = str_replace(APP_PATH, $theme->getPath(), $module_file_path);
        // 如果未被继承则还原为原Module模板文件
        if (!is_file($theme_file_path)) {
            $theme_file_path = $module_file_path;
        }
        // TODO 处理非开发模式 静态文件的读取位置 包含了代码绝对路径问题 应该读取主题中的绝对路径 并返回静态文件的URL

        $fileData->setData('filename', $theme_file_path);
    }
}
