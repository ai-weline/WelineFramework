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

    function __construct(array $data = [])
    {
        parent::__construct($data);
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
        $this->setData('template', $template);
        return $this;
    }

    function getTemplate(): string
    {
        return $this->getData('template');
    }

    function getTemplateEngine(): ?Template
    {
        return $this->engine;
    }

    function render()
    {
        return $this->engine->getFetchFile($this->getTemplate());
    }

    function __toString()
    {
        $block = $this;
        require $this->render();
    }
}