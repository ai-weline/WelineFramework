<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\View;

use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\View\Cache\ViewCache;

class  Block extends DataObject implements BlockInterface
{
    protected ?CacheInterface $_cache = null;
    protected ?Template $engine = null;

    function __construct(string $template = null, array $data = [])
    {
        parent::__construct($data);
        if ($template) $this->setTemplate($template);
        if (empty($this->_cache)) $this->_cache = ObjectManager::getInstance(ViewCache::class . 'Factory');
        if (empty($this->engine)) $this->engine = Template::getInstance()->init();
    }

    function setTemplate(string $template)
    {
        if (is_bool(strpos($template, '::'))) {
            throw new Exception(__('模板文件设置错误：%1,正确示例：Weline_System::demo.phtml'));
        }
        $template_arr = explode('::', $template);
        $template_module_name = array_shift($template_arr);
        # 设置模板位置
        $this->engine->setViewDir($template_module_name . DIRECTORY_SEPARATOR . 'view');
        $this->setData('template', $template);
        return $this;
    }

    function getTemplate():string
    {
        return $this->getData('template');
    }

    function getTemplateEngine(): ?Template
    {
        return $this->engine;
    }

//    /**
//     * @DESC         |获取模板渲染
//     *
//     * 参数区：
//     *
//     * @param string|null $fileName
//     * @return void
//     */
//    private function fetch(string $fileName)
//    {
//        $module_cache_key = $this::class . '_module';
//        $module = $this->_cache->get($module_cache_key);
//        if (is_null($module)) {
//            $module = explode('\\', $this::class);
//        }
//        $fetch_file_name_cache_key = 'fetch_file_name_cache_key_' . $fileName;
//        $cache_file_name = $this->_cache->get($fetch_file_name_cache_key);
//        if ($cache_file_name) {
//            $fileName = $cache_file_name;
//        } else {
//            if ($fileName === null) {
//                $parent_call_info = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
//                $fileNameArr = explode(\Weline\Framework\Controller\Data\DataInterface::dir, $parent_call_info['class']);
//                $fileName = trim(array_pop($fileNameArr), '\\') . DIRECTORY_SEPARATOR . $parent_call_info['function'];
//            } elseif (is_bool(strpos($fileName, '/')) || is_bool(strpos($fileName, '\\'))) {
//                $parent_call_info = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
//                $fileNameArr = explode(\Weline\Framework\Controller\Data\DataInterface::dir, $parent_call_info['class']);
//                $fileName = trim(array_pop($fileNameArr), '\\') . DIRECTORY_SEPARATOR . $fileName;
//            }
//            $this->_cache->set($fetch_file_name_cache_key, $fileName);
//        }
//
//        return $this->engine->fetch($fileName);
//    }

    function render()
    {
        return $this->engine->fetchTemplateTagSource('templates', $this->getTemplate());
    }

    function __toString()
    {
        return $this->render();
    }
}