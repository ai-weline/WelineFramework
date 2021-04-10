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
    )
    {
        $this->welineTheme = $welineTheme;
        $this->themeCache = $themeCache->create();
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

        // 非开发模式 判断缓存中是否存在 主题文件，存在则直接返回 不存在则解析主题文件
        if (!DEV && $theme_file_path = $this->themeCache->get($module_file_path)) {
            $fileData->setData('filename', $theme_file_path);
        }

        # 开始分析主题路径
        try {
            $theme = $this->welineTheme->getActiveTheme();
        } catch (DataNotFoundException $e) {
            if (DEV) {
                throw  new Exception(__('主题数据找不到:') . $e->getMessage());
            }
        } catch (ModelNotFoundException $e) {
            if (DEV) {
                throw  new Exception(__('主题Mode找不到:') . $e->getMessage());
            }
        } catch (DbException $e) {
            if (DEV) {
                throw  new Exception(__('数据库异常：') . $e->getMessage());
            }
        } finally {
            $theme = $this->welineTheme->setName('default')
                ->setPath('default')
                ->setIsActive(1);
        }

        // 组织主题文件位置
        $theme_file_path = str_replace(APP_PATH, $theme->getPath(), $module_file_path);
        // 非开发模式启用缓存
        if (is_file($theme_file_path)) {
            if (!DEV) {
                $this->themeCache->set($module_file_path, $theme_file_path);
            }
        } else {
            $theme_file_path = $module_file_path;
        }
        $fileData->setData('filename', $theme_file_path);
    }
}
