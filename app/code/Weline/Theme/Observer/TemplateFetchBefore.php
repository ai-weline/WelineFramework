<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Theme\Observer;

use think\db\exception\DataNotFoundException;
use think\db\exception\DbException;
use think\db\exception\ModelNotFoundException;
use Weline\Framework\App\Exception;
use Weline\Framework\Event\Event;
use Weline\Framework\Event\ObserverInterface;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\View\Template;
use Weline\Theme\Model\WelineTheme;

class TemplateFetchBefore implements ObserverInterface
{
    public function execute(Event $event)
    {
        # 开始分析主题路径
        /**@var Template $template*/
        $template = $event->getData('object');
        $data     = $event->getData('data');
        $filename = $data->getData('filename');
        $data->setData('filename', $filename);

        return $filename;
        // 主题数据 存在缓存
        /**@var WelineTheme $welineTheme */
        $welineTheme = ObjectManager::getInstance(WelineTheme::class);

        try {
            $theme = $welineTheme->getActiveTheme();
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
            $theme = $welineTheme->setName('default')
                ->setPath('default')
                ->setIsActive(1);
        }
        // 组织主题文件位置
        $theme_path = $theme->getPath() . $module_file_path;

        return $theme_path;
    }
}
