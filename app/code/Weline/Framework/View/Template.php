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

    const file_ext = '.phtml';

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

    /**
     * @var array $vars 读取模板中所有变量的数组
     */
    private array $vars = [];

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

    function getBlock(string $class)
    {
        return ObjectManager::getInstance($class);
    }

    public function init()
    {
        $this->_request      = ObjectManager::getInstance(Request::class);
        $this->view_dir      = BP . $this->_request->getRouterData('module_path') . DataInterface::dir . DIRECTORY_SEPARATOR;
        $this->vars['title'] = $this->_request->getModuleName();

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
                $tplFile = $template_dir . $fileName . self::file_ext;
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
                $comFileName = $baseComFileDir . 'com_' . $file_name . self::file_ext;
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
        if (is_int(strpos($comFileName, '\\'))) str_replace('\\', DIRECTORY_SEPARATOR, $comFileName);
        if (is_int(strpos($comFileName, '//'))) str_replace('/', DIRECTORY_SEPARATOR, $comFileName);
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
    public function fetch(string $fileName, array $dictionary = [])
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
    private function tmp_replace(string $content, string $fileName): array|string|null
    {
        $static_url_path = $this->getUrlPath($this->statics_dir);
        $replaces        = [
            '__static__' => $static_url_path,
            '__STATIC__' => $static_url_path,
            '<php>'      => '<?php ',
            '</php>'     => '?>',
        ];
        foreach ($replaces as $tag => $replace) {
            $content = str_replace($tag, $replace, $content);
        }
        $patterns = [
            '/\<\!--\s*\$([a-zA-Z]*)\s*--\>/',
            '/\@\{(.*)\}/',
            '/\@include (.*)/',
            '/\@block\((.*)\)/',
            '/\@template\((.*)\)/',
            '/\@static\((.*)\)/',
            '/\@view\((.*)\)/',
            '/\@p\((.+)\)/',
            '/\@controller\((.+)\)/',
        ];
        # 开发环境实时PHP代码输出资源
//        if (DEV) {
//            $dev_replacement = [
        /*                '<?php echo $this->vars["${1}"]; ?>',*/
        /*                '<?php ${1} ?>',*/
        /*                '<?php include(trim("${1}")); ?>',*/
        /*                '<?php echo $this->getBlock(trim("${1}"));//打印Block块对象 ?>',*/
        /*                '<?php echo $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE,trim("${1}"));// 读取资源文件 ?>',*/
        /*                '<?php echo $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS,trim("${1}"));// 读取资源文件 ?>',*/
        /*                '<?php echo $this->fetch(trim("${1}")); ?>',*/
        /*                '<?php p(isset($this->getData("${1}"))?:${1}); ?>',*/
//            ];
//            return preg_replace($patterns, $dev_replacement, $content);
//        }
        return preg_replace_callback($patterns, function ($back) use ($fileName) {
            $back[0]    = str_replace($back[1], '', $back[0]);
            $re_content = '';
            switch (strtolower($back[0])) {
                case '@controller()':
                    # FIXME 尚未想好的代码分支
                    $cache_key = $this->viewCache->buildKey($fileName);
                    # 控制解析
                    $pipe_str = explode(',', trim($back[1]));
                    foreach ($pipe_str as $pipe) {
                        $pip = explode(':', $pipe);
                        if (2 === count($pip)) {
                            switch ($pip[0]) {
                                case 'cache':
                                    if (!is_numeric($pip[1])) throw new Exception(__('缓存值类型设置错误：%1 应当设置为整数，单位（毫秒） 设置文件：%2 正确设置示例：cache:60'));
                                    if (!$this->viewCache->get($cache_key)) {
                                        list($comFile, $tplFile) = $this->convertFetchFileName($fileName);
                                        $re_content .= "<?php \$cache_key=\$this->viewCache->buildKey('{$fileName}');if(\$this->viewCache->get(\$cache_key)){echo \$this->viewCache->get(\$cache_key);}else{echo {file_get_contents($comFile)}}?>";
                                        $this->viewCache->set($cache_key, $re_content . file_get_contents($comFile), $pip[1]);
                                    }
                                case 'ifconfig':
                                default;
                            }
                        }
                    }

//                    $re_content = $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($back[1]));
                    break;
                case '@static()':
                    $re_content = $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($back[1]));
                    break;

                case '@block()':
                    $re_content = $this->getBlock(trim($back[1]))->__toString();
                    break;

                case '@template()':
                    $re_content = file_get_contents($this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($back[1])));
                    break;

                case '@include()':
                    $re_content = file_get_contents(trim($back[1]));
                    break;

                case '@view()':
                    $re_content = $this->fetch(trim($back[1]));
                    break;

                case '@p()':
                    $re_content = "<?php p($back[1])?>";
                    break;

            }
            return $re_content;
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
        $pre = $this->_request->getBaseHost() . '/';
        if ($this->_request->isBackend()) {
            $pre .= Env::getInstance()->getConfig('admin') . '/';
        }
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

