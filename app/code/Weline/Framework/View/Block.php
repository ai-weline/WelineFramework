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

class Block extends Template implements BlockInterface
{
    public ?CacheInterface $_cache = null;
    protected ?Template $engine = null;
    protected string $_template = '';

    public function __construct(array $data = [])
    {
        parent::__construct($data);
        if (empty($this->_cache)) {
            $this->_cache = ObjectManager::getInstance(ViewCache::class . 'Factory');
        }
        if (empty($this->engine)) {
            $this->engine = Template::getInstance()->init();
        }
    }

    public function setTemplate(string $template = '')
    {
        if (empty($template) && isset($this->_template)) {
            $template = $this->_template;
        }
        if (is_bool(strpos($template, '::'))) {
            throw new Exception(__('模板文件设置错误：%1,正确示例：Weline_System::demo.phtml'));
        }
        $template_arr         = explode('::', $template);
        $template_module_name = array_shift($template_arr);
        # 设置模板位置
        $this->setData('template', $template);
        return $this;
    }

    public function getTemplate(): string
    {
        $template = $this->getData('template');
        if (empty($template) && isset($this->_template)) {
            $template = $this->_template;
        }
        return $template;
    }

    public function getTemplateEngine(): ?Template
    {
        return $this->engine;
    }

    /**
     * @throws \Exception
     */
    public function render()
    {
        return $this->fetchHtml($this->getTemplate(), ['block' => $this]);
    }

    /**
     * @DESC         |调用模板显示 FIXME 等待抽象出模板引擎的基础类，并继承，解决block模板中使用$this指向block本身类的问题，此方法和模板类效果一样，属于代码冗余
     *
     * 参数区：
     *
     * @param string $fileName   获取的模板名
     * @param array  $dictionary 参数绑定
     *
     * @return bool|void
     * @throws \Exception
     */
    public function fetchHtml(string $fileName, array $dictionary = [])
    {
        $comFileName = $this->engine->getFetchFile($fileName);
        ob_start();
        try {
            if ($dictionary) {
                $this->addData($dictionary);
            }
            # 将数组存储的变量散列到当前页内存中，使得变量可在页面中暴露出来（可直接使用）
            if ($this->getData()) {
                extract($this->getData(), EXTR_SKIP);
            }
            include $comFileName;
        } catch (\Exception $exception) {
            ob_end_clean();
            throw $exception;
        }
        /** Get output buffer. */
        # FIXME 是否显示模板路径
        return ob_get_clean();
    }

    public function __toString()
    {
        return $this->render();
    }
}
