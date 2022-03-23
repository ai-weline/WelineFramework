<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\View;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\App\System;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Controller\PcController;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Exception\Core;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Module\ModuleInterface;
use Weline\Framework\Output\Debug\Printing;
use Weline\Framework\Session\Session;
use Weline\Framework\Ui\FormKey;
use Weline\Framework\View\Cache\ViewCache;
use Weline\Framework\View\Data\DataInterface;
use Weline\Framework\View\Data\HtmlInterface;
use Weline\SystemConfig\Model\SystemConfig;

class Template extends DataObject
{
    use TraitTemplate;

    private string $file_ext = '.phtml';

    protected Request $_request;
    private Session $session;

    /**
     * @var PcController
     */
    private PcController $controller;

    /**
     * @var string 指定模板目录
     */
    private string $template_dir = DataInterface::view_TEMPLATE_DIR;

    /**
     * @var string 编译后的目录
     */
    private string $compile_dir = DataInterface::view_TEMPLATE_COMPILE_DIR;

    /**
     * @var string 静态文件目录
     */
    private string $statics_dir = DataInterface::view_STATICS_DIR;

    /**
     * @var string 静态文件目录
     */
    private string $view_dir;

    private array $theme;

    private EventsManager $eventsManager;

    /**
     * @var CacheInterface 缓存
     */
    private CacheInterface $viewCache;

    private static Template $instance;

    private function __clone()
    {
    }

    final static function getInstance(): Template
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    /**
     * @DESC          # 读取模板文件拓展
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/3/20 20:18
     * 参数区：
     * @return string
     */
    function getFileExt(): string
    {
        return $this->file_ext;
    }

    /**
     * @DESC          # 设置模板文件拓展
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/3/20 20:17
     * 参数区：
     *
     * @param string $ext 拓展：例如.phtml则填写phtml
     *
     * @return $this
     */
    function setFileExt(string $ext): static
    {
        $this->file_ext = '.' . $ext;
        return $this;
    }

    function getBlock(string $class)
    {
        return ObjectManager::getInstance($class);
    }

    public function init()
    {
        $this->_request = ObjectManager::getInstance(Request::class);
        $this->view_dir = $this->_request->getRouterData('module_path') . DataInterface::dir . DIRECTORY_SEPARATOR;
        $this->setData('title', $this->_request->getModuleName());

        $this->theme         = Env::getInstance()->getConfig('theme', Env::default_theme_DATA);
        $this->eventsManager = ObjectManager::getInstance(EventsManager::class);
        $this->viewCache     = ObjectManager::getInstance(ViewCache::class)->create();

        $this->statics_dir  = $this->getViewDir(DataInterface::view_STATICS_DIR);
        $this->template_dir = $this->getViewDir(DataInterface::view_TEMPLATE_DIR);
        $this->compile_dir  = $this->getViewDir(DataInterface::view_TEMPLATE_COMPILE_DIR);
        return $this;
    }

    /**
     * @DESC          # 获取form_key
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/10/22 22:24
     * 参数区：
     * @return string
     */
    function getFormKey($url): string
    {
        return ObjectManager::getInstance(FormKey::class)->getHtml($url);
    }

    /**
     * @DESC         |获取视图文件
     *
     * 参数区：
     *
     * @param $filepath
     *
     * @return string
     * @throws \Exception
     */
    public function getViewFile($filepath): string
    {
        $path = $this->view_dir . $filepath;
        if (!file_exists($path) && DEV) {
            new Exception(__('文件不存在！位置：') . $path);
        }
        $this->fetch($filepath);

        return $path;
    }

    /**
     * @DESC         |模板中变量分配调用的方法
     *
     * 参数区：
     *
     * @param string|array $key 键值
     * @param null         $value
     *
     * @return Template
     */
    public function assign(string|array $key, mixed $value = null): static
    {
        $this->setData($key, $value);
        return $this;
    }

    /**
     * @param string $fileName 文件名
     *
     * @throws Core
     * @throws Exception
     */
    function convertFetchFileName(string $fileName): array
    {
        $comFileName_cache_key = $this->view_dir . $fileName . '_comFileName';
        $tplFile_cache_key     = $this->view_dir . $fileName . '_tplFile';
        $comFileName           = '';
        $tplFile               = '';
        # 让非生产环境实时读取文件
        if (PROD) {
            $comFileName = $this->viewCache->get($comFileName_cache_key);
            $tplFile     = $this->viewCache->get($tplFile_cache_key);
        }

        # 测试
//        file_put_contents(__DIR__ . '/test.txt', $comFileName . PHP_EOL, FILE_APPEND);
        // 编译文件不存在的时候 重新对文件进行处理 防止每次都处理
        if (empty($comFileName) || empty($tplFile)) {
            // 解析模板路由
            $fileName          = str_replace('/', DIRECTORY_SEPARATOR, $fileName);
            $file_name_dir_arr = explode(DIRECTORY_SEPARATOR, $fileName);
            $file_dir          = '';
            $file_name         = '';

            // 如果给的文件名字有路径
            if (count($file_name_dir_arr) > 1) {
                $file_name = array_pop($file_name_dir_arr);
                $file_dir  = implode(DIRECTORY_SEPARATOR, $file_name_dir_arr);
                if ($file_dir) {
                    $file_dir .= DIRECTORY_SEPARATOR;
                }
            }
            // 判断文件后缀
            $file_ext = substr(strrchr($fileName, '.'), 1);

            # 检测读取别的模块的模板文件
            list($fileName, $file_dir, $view_dir, $template_dir, $compile_dir) = $this->processFileSource($fileName, $file_dir);

            // 检测模板文件：如果文件名有后缀 则直接到view下面读取。没有说明是默认
            if ($file_ext) {
                $tplFile = $view_dir . $fileName;
            } else {
                $tplFile = $template_dir . $fileName . $this->getFileExt();
            }
            $tplFile = $this->fetchFile($tplFile);

            if (!file_exists($tplFile)) {
                throw new Exception(__('获取操作：%1，模板文件：%2 不存在！源文件：%3', [$fileName, $tplFile, $tplFile]));
            }

            // 检测目录是否存在,不存在则建立
            $baseComFileDir = $compile_dir . ($file_dir ?: '');
            if (!is_dir($baseComFileDir)) {
                mkdir($baseComFileDir, 0770, true);
            }

            //定义编译合成的文件 加了前缀 和路径 和后缀名.phtml
            $file_name = $file_name ?? $fileName;
            if ($file_ext) {
                $comFileName = $baseComFileDir . 'com_' . $file_name;
            } else {
                $comFileName = $baseComFileDir . 'com_' . $file_name . $this->getFileExt();
            }
            $comFileName = $this->fetchFile($comFileName);
            # 生产模式缓存: 根据管道设置缓存
            if (PROD) {
                $this->viewCache->set($comFileName_cache_key, $comFileName);
                $this->viewCache->set($tplFile_cache_key, $tplFile);
            };
        }

        # 测试
//        file_put_contents(__DIR__ . '/test.txt', $comFileName . PHP_EOL, FILE_APPEND);
        if (is_int(strpos($comFileName, '\\'))) $comFileName = str_replace('\\', DIRECTORY_SEPARATOR, $comFileName);
        if (is_int(strpos($comFileName, '//'))) $comFileName = str_replace('//', DIRECTORY_SEPARATOR, $comFileName);
        return [$comFileName, $tplFile];
    }


    function getFetchFile(string $fileName, string $module_name = ''): string
    {
        list($comFileName, $tplFile) = $this->convertFetchFileName($fileName);
        # 检测编译文件，如果不符合条件则重新进行文件编译
        if (DEV || !file_exists($comFileName) || (filemtime($comFileName) < filemtime($tplFile))) {
            //如果缓存文件不存在则 编译 或者文件修改了也编译
            $repContent = $this->tmp_replace(file_get_contents($tplFile), $fileName);//得到模板文件 并替换占位符 并得到替换后的文件
            file_put_contents($comFileName, $repContent);                            //将替换后的文件写入定义的缓存文件中
        }
        return $comFileName;
    }

    /**
     * @DESC         |调用模板显示
     *
     * 参数区：
     *
     * @param string $fileName   获取的模板名
     * @param array  $dictionary 参数绑定
     *
     * @return bool|void
     * @throws \Exception
     */
    public function fetch(string $fileName)
    {
        /** Get output buffer. */
        return $this->fetchHtml($fileName);
    }

    /**
     * @DESC         |调用模板显示
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
        $comFileName = $this->getFetchFile($fileName);
        ob_start();
        try {
            extract($dictionary, EXTR_SKIP);
            $this->setData($dictionary);
            include $comFileName;
        } catch (\Exception $exception) {
            ob_end_clean();
            throw $exception;
        }
        /** Get output buffer. */
        # FIXME 是否显示模板路径
        return ob_get_clean();
    }

    /**
     * @DESC         |替换模板中的占位符
     *
     * 参数区：
     *
     * @param string $content  文本
     * @param string $fileName 模板文件
     *
     * @return string|string[]|null
     * @throws Core
     */
    private function tmp_replace(string $content): array|string|null
    {
        # 系统自带的
        $template_elements = [
            'php'       => function ($back) {
                return '<?php ' . trim($back[1]) . '?>';
            },
            'foreach'   => function ($back) {
                preg_match('/name=[\'|"](.*?)[\'|"]/', $back['attrs'], $name);
                $name = array_pop($name);
                preg_match('/key=[\'|"](.*?)[\'|"]/', $back['attrs'], $key);
                $key = array_pop($key);
                $key = $key??'key';
                preg_match('/item=[\'|"](.*?)[\'|"]/', $back['attrs'], $item);
                $item = array_pop($item);
                return <<<FOREACH
<!-- foreach 属性： {$back['attrs']} -->
<?php
        foreach (\$this->getData('{$name}') as \${$key} => \${$item}) {
        ?>
                    {$this->tmp_replace($back['content'])}
                    <?php
                }
?>
FOREACH;
            },
            'template'  => function ($back) {
                return file_get_contents($this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($back[1])));
            },
            'var'       => function ($back) {
                return '<?=' . trim($back[1]) . '?>';
            },
            'pp'        => function ($back) {
                return "<?php p({$back[1]})?>";
            },
            'include'   => function ($back) {
                return file_get_contents($this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($back[1])));
            },
            'block'     => function ($back) {
                return $this->getBlock(trim($back[1]))->__toString();
            },
            'static'    => function ($back) {
                return $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($back[1]));
            },
            'hook',
            'js'        => function ($back) {
                $source = $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($back[1]));
                return "<script src='{$source}'></script>";
            },
            'css'       => function ($back) {
                $source = $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($back[1]));
                return "<link href=\"{$source}\" rel=\"stylesheet\" type=\"text/css\"/>";
            },
            'lang'      => function ($back) {
                return '<?=__(\'' . trim($back[1]) . '\')?>';
            },
            'url'       => function ($back) {
                return "<?=\$this->getUrl({$back[1]})?>";
            },
            'admin-url' => function ($back) {
                return "<?=\$this->getAdminUrl({$back[1]})?>";
            },
        ];
        /**@var EventsManager $event */
        $event = ObjectManager::getInstance(EventsManager::class);
        $data  = (new DataObject(['template' => $this, 'template_elements' => $template_elements]));
        $event->dispatch('Framework_Template::after_template_patterns_callback_map', ['data' => $data]);
        $template_elements = $data->getData('template_elements');

        // 替换函数
        $patternsSynonymous     = function (string $template_element, &$replace_call_back) {
            return [
                'patterns'                   => [
                    '/<w-' . $template_element . '([\s\S]*?)<\/w-' . $template_element . '>/m',
                    '/<' . $template_element . '([\s\S]*?)<\/' . $template_element . '>/m',
                    '/\@' . $template_element . '\(([\s\S]*?)\)/m',

                    '/\@' . $template_element . '\{([\s\S]*?)\}/m',
                ],
                'replace_match_and_callback' => [
                    '<w-' . $template_element . '</w-' . $template_element . '>' => $replace_call_back,
                    '<' . $template_element . '</' . $template_element . '>'     => $replace_call_back,
                    '@' . $template_element . '()'                               => $replace_call_back,
                    '@' . $template_element . '{}'                               => $replace_call_back,
                ]
            ];
        };
        $patterns               = [];
        $patterns_and_callbacks = [];
        foreach ($template_elements as $template_element => $replace_call_back) {
            $pattern_and_callback   = $patternsSynonymous($template_element, $replace_call_back);
            $patterns               = array_merge($patterns, $pattern_and_callback['patterns']);
            $patterns_and_callbacks = array_merge($patterns_and_callbacks, $pattern_and_callback['replace_match_and_callback']);
        }
        # 开发环境实时PHP代码输出资源
        return preg_replace_callback($patterns, function ($back) use ($patterns_and_callbacks) {
            $back[0]         = str_replace($back[1], '', $back[0]);
            $back_arr        = explode('>', $back[1]);
            $back[1]         = ltrim($back[1], '>');
            $attrs           = array_shift($back_arr);
            $content         = $back_arr ? ltrim(implode('>', $back_arr), '>') : $attrs;
            $back['content'] = $content;
            $back['attrs']   = $attrs;
            $re_content      = '';
            if (isset($patterns_and_callbacks[$back[0]]) && $callback = $patterns_and_callbacks[$back[0]]) {
                $re_content = $callback($back);
            }
            /**@var EventsManager $event */
            $event = ObjectManager::getInstance(EventsManager::class);
            $data  = (new DataObject(['back' => $back, 'content' => $re_content, 'object' => $this]));
            $event->dispatch('Framework_Template::after_template_replace', ['data' => $data]);
            return $data->getData('content');
        },                           $content);
    }

    public function getUrl(string $path, array $params = [], bool $merge_query = true): string
    {
        return $this->_request->getUrl($path, $params, $merge_query);
    }

    function getAdminUrl(string $path, array|bool $params = []): string
    {
        if (empty($path)) {
            return $this->_request->getCurrentUrl();
        }
        $pre = $this->_request->getBaseHost() . '/' . Env::getInstance()->getConfig('admin') . '/';
//        $pre = $this->_request->getBaseHost() . '/';
//        if ($this->_request->isBackend()) {
//            $pre .= Env::getInstance()->getConfig('admin') . '/';
//        }
        $path = rtrim($pre . $path, '/');
        if (empty($params)) {
            return $path;
        }
        if (is_array($params)) {
            return $path . '?' . http_build_query($params);
        }
        return $path;
    }

    function getRequest(): Request
    {
        return ObjectManager::getInstance(Request::class);
    }

}

