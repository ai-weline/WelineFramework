<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Controller;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Controller\Cache\ControllerCache;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\View\Data\DataInterface;
use Weline\Framework\View\Template;
use ReflectionObject;

class PcController extends Core
{
    private Template $_template;
    private EventsManager $_eventManager;

    private CacheInterface $controllerCache;

    function __init()
    {
        parent::__init();
        $this->isAllowed();
        $this->assign($this->_request->getParams());
        if (empty($this->controllerCache)) $this->controllerCache = $this->getControllerCache();
        if (empty($this->_eventManager)) $this->_eventManager = ObjectManager::getInstance(EventsManager::class);
    }

    /**
     * @param string|int $url url或者http状态码
     * @return void
     * @throws Exception
     * @throws \ReflectionException
     */
    function redirect(string|int $url)
    {
        if(is_string($url)){
            $this->getRequest()->getResponse()->redirect($url);
        }elseif($url=404){
            $this->getRequest()->getResponse()->responseHttpCode($url);
        }
    }

    function isAllowed(): void
    {
        # FIXME 存储需要验证的URL才合理 放入SESSION不合理
        if (!empty($form_key_paths_str = $this->getSession()->getData('form_key_paths')) && !empty($form_key = $this->getSession()->getData('form_key'))) {
            $form_key_paths = explode(',', $form_key_paths_str);
            if (in_array($this->getRequest()->getUrl(), $form_key_paths) && ($form_key !== $this->getRequest()->getParam('form_key'))) {
                $this->noRouter();
            }
        }
    }

    public function getControllerCache(): CacheInterface
    {
        if (!isset($this->controllerCache)) {
            $this->controllerCache = ObjectManager::getInstance(ControllerCache::class)->create();
        }
        return $this->controllerCache;
    }

    /**
     * 设置
     * @param Template $template
     * @return PcController
     */
    public function setTemplate(Template $template): static
    {
        $this->_template = $template;

        return $this;
    }

    /**
     * @DESC         |获取模板数据
     *
     * 参数区：
     *
     * @param string|null $key
     * @return mixed
     * @throws Exception
     * @throws \ReflectionException
     */
    public function getData(string $key = null): mixed
    {
        return $this->getTemplate()->getData($key);
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return Template
     * @throws Exception
     * @throws \ReflectionException
     */
    public function getTemplate(): Template
    {
        if (!isset($this->_template)) {
            $this->_template = Template::getInstance()->init();
        }
        return $this->_template;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param array|string $tpl_var
     * @param array|string|null $value
     * @return PcController
     */
    protected function assign(array|string $tpl_var, mixed $value = null): static
    {
        if (is_string($tpl_var)) {
            $this->getTemplate()->assign($tpl_var, $value);
        }
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $key => $item) {
                $this->getTemplate()->assign($key, $item);
            }
        }

        return $this;
    }

    /**
     * @DESC         |获取模板渲染
     *
     * 参数区：
     *
     * @param string|null $fileName
     * @return void
     */
    protected function fetch(string $fileName = null): mixed
    {
        # 如果指定了模板就直接读取
        if ($fileName && strpos($fileName, '::')) {
            return $this->getTemplate()->fetch($fileName);
        }
        $controller_class_name = $this->_request->getRouterData('class/controller_name');
        if ($fileName === null) {
            $fileName = $controller_class_name . '/' . $this->_request->getRouterData('class/method');
        } elseif (is_bool(strpos($fileName, '/')) || is_bool(strpos($fileName, '\\'))) {
            $fileName = $controller_class_name . DIRECTORY_SEPARATOR . $fileName;
        }
        return $this->getTemplate()->fetch($fileName);
    }

    /**
     * 返回JSON
     * @param string $data
     * @return string
     */
    protected function fetchJson(array $data): string
    {
        return json_encode($data);
    }

    /**
     * @DESC         |按照类型获取view目录
     *
     * 参数区：
     *
     * @return string
     */
    public function getViewBaseDir(): string
    {
        $cache_key = 'module_of_' . $this::class;
        // 设置缓存，以免每次都去反射解析控制器的模块基础目录
        if ($module_dir = $this->getControllerCache()->get($cache_key)) {
            return $module_dir;
        }
        $reflect = new ReflectionObject($this);
        $filename = $reflect->getFileName();
        $filename = str_replace(Env::GENERATED_DIR, 'app', $filename);
        $ctl_dir_reflect_arr = explode(self::dir, $filename);
        $module_dir = array_shift($ctl_dir_reflect_arr);
        $module_dir = $module_dir . DataInterface::dir . DIRECTORY_SEPARATOR;
        if (!is_dir($module_dir)) {
            mkdir($module_dir, 0775, true);
        }
        $this->getControllerCache()->set($cache_key, $module_dir);

        return $module_dir;
    }
}
