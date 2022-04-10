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
use Weline\Framework\Hook\Hooker;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Module\ModuleInterface;
use Weline\Framework\Output\Debug\Printing;
use Weline\Framework\Session\Session;
use Weline\Framework\Ui\FormKey;
use Weline\Framework\View\Cache\ViewCache;
use Weline\Framework\View\Data\DataInterface;
use Weline\Framework\View\Data\HtmlInterface;
use Weline\Framework\View\Exception\TemplateException;
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

    final public static function getInstance(): Template
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
    public function getFileExt(): string
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
    public function setFileExt(string $ext): static
    {
        $this->file_ext = '.' . $ext;
        return $this;
    }

    public function getBlock(string $class)
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
    public function getFormKey($url): string
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
    public function convertFetchFileName(string $fileName): array
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
        if (is_int(strpos($comFileName, '\\'))) {
            $comFileName = str_replace('\\', DIRECTORY_SEPARATOR, $comFileName);
        }
        if (is_int(strpos($comFileName, '//'))) {
            $comFileName = str_replace('//', DIRECTORY_SEPARATOR, $comFileName);
        }
        return [$comFileName, $tplFile];
    }


    public function getFetchFile(string $fileName, string|null $module_name = ''): string
    {
        list($comFileName, $tplFile) = $this->convertFetchFileName($fileName);
        # 检测编译文件，如果不符合条件则重新进行文件编译
        if (DEV || !file_exists($comFileName) || (filemtime($comFileName) < filemtime($tplFile))) {
            //如果缓存文件不存在则 编译 或者文件修改了也编译
            $content    = file_get_contents($tplFile);
            $repContent = $this->tmp_replace($content, $comFileName);                   //得到模板文件 并替换占位符 并得到替换后的文件
            file_put_contents($comFileName, $repContent);                               //将替换后的文件写入定义的缓存文件中
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
        return $this->ob_file($comFileName, $dictionary);
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
    public function fetchTagHtml(string $tag, string $fileName, array $dictionary = [])
    {
        $comFileName = $this->fetchTagSource($tag, $fileName);
        return $this->ob_file($comFileName, $dictionary);
    }

    public function ob_file(string $filename, array $dictionary = []): string
    {
        ob_start();
        try {
            if ($dictionary) {
                $this->addData($dictionary);
            }
            # 将数组存储的变量散列到当前页内存中，使得变量可在页面中暴露出来（可直接使用）
            if ($this->getData()) {
                extract($this->getData(), EXTR_SKIP);
            }
            include $filename;
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
    private function tmp_replace(string $content, string $fileName = ''): array|string|null
    {
        # 系统自带的标签
        $template_elements = [
            'php'         => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => "<?php ",
                            'tag-end'   => "?>",
                            default     => "<?php {$tag_data[1]} ?>"
                        };
                    }
            ],
            'w-php'       => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => "<?php ",
                            'tag-end'   => "?>",
                            default     => "<?php {$tag_data[1]} ?>"
                        };
                    }
            ],
            'include'     => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => "<?php include(",
                            'tag-end'   => ");?>",
                            default     => "<?php include({$tag_data[1]});?>"
                        };
                    }
            ],
            'w-include'   => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => "<?php include(",
                            'tag-end'   => ");?>",
                            default     => "<?php include({$tag_data[1]});?>"
                        };
                    }
            ],
            'var'         => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => '<?= ',
                            'tag-end'   => '?>',
                            default     => "<?php echo {$tag_data[1]} ?>"
                        };
                    }
            ],
            'w-var'       => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag-start' => '<?= ',
                            'tag-end'   => '?>',
                            default     => "<?php echo {$tag_data[1]} ?>"
                        };
                    }
            ],
            'pp'          => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        switch ($tag_key) {
                            case '@tag{}':
                            case '@tag()':
                                $var_name = $tag_data[1];
                                if (!str_starts_with($var_name, '$')) {
                                    $var_name .= '$' . $var_name;
                                }
                                return "<?=p({$var_name})?>";
                            case 'tag-start':
                                return "<?=p(";
                            case 'tag-end':
                                return ")?>";
                        }
                        return '';
                    }],
            'w-pp'        => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        switch ($tag_key) {
                            case '@tag{}':
                            case '@tag()':
                                $var_name = $tag_data[1];
                                if (!str_starts_with($var_name, '$')) {
                                    $var_name .= '$' . $var_name;
                                }
                                return "<?=p({$var_name})?>";
                            case 'tag-start':
                                return "<?=p(";
                            case 'tag-end':
                                return ")?>";
                        }
                        return '';
                    }],
            'if'          => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'attr'      => ['condition' => 1],
                'callback'  => function ($tag_key, $config, $tag_data, $attributes) {
                    $result = '';
                    switch ($tag_key) {
                        // @if{$a === 1=><li><var>$a</var></li>|$a===2=><li><var>$a</var></li>}
                        case '@tag{}':
                        case '@tag()':
                            $content_arr = explode('|', $tag_data[1]);
                            foreach ($content_arr as &$item) {
                                $item = explode('=>', $content_arr[0]);
                            }
                            if (1 === count($content_arr)) {
                                $result = "<?php if({$content_arr[0][0]}):{$content_arr[0][1]};endif;?>";
                            }
                            if (1 < count($content_arr)) {
                                $result = "<?php if({$content_arr[0][0]}):{$content_arr[0][1]};endif;?>";
                            }
                            foreach ($content_arr as $key => $data) {
                                if (0 === $key) {
                                    $result = "<?php if($data[0]):?>" . $data[1];
                                } else {
                                    if (count($data) > 1) {
                                        $result .= "<?php elseif($data[0]):?>" . $data[1];
                                    } else {
                                        $result .= "<?php else:?>" . $data[0];
                                    }
                                }
                            }
                            $result .= '<?php endif;?>';
                            break;
                        case 'tag-self-close':
                            throw new TemplateException(__('if没有自闭合标签。示例：%1', '<if condition="$a>$b"><var>a</var><elseif condition="$b>$a"/><var>b</var><else/><var>a</var><var>b</var></if>'));
                        case 'tag-start':
                            $condition = $attributes['condition'];
                            $result    = "<?php if({$condition}):?>";
                            break;
                        case 'tag-end':
                            $result = '<?php endif;?>';
                            break;
                        default:
                    }
                    return $result;
                }],
            'w-if'        => [
                'tag-start' => 1,
                'tag-end'   => 1,
                'attr'      => ['condition' => 1],
                'callback'  => function ($tag_key, $config, $tag_data, $attributes) {
                    $result = '';
                    switch ($tag_key) {
                        // @if{$a === 1=><li><var>$a</var></li>|$a===2=><li><var>$a</var></li>}
                        case '@tag{}':
                        case '@tag()':
                            $content_arr = explode('|', $tag_data[1]);
                            foreach ($content_arr as &$item) {
                                $item = explode('=>', $content_arr[0]);
                            }
                            if (1 === count($content_arr)) {
                                $result = "<?php if({$content_arr[0][0]}):{$content_arr[0][1]};endif;?>";
                            }
                            if (1 < count($content_arr)) {
                                $result = "<?php if({$content_arr[0][0]}):{$content_arr[0][1]};endif;?>";
                            }
                            foreach ($content_arr as $key => $data) {
                                if (0 === $key) {
                                    $result = "<?php if($data[0]):?>" . $data[1];
                                } else {
                                    if (count($data) > 1) {
                                        $result .= "<?php elseif($data[0]):?>" . $data[1];
                                    } else {
                                        $result .= "<?php else:?>" . $data[0];
                                    }
                                }
                            }
                            $result .= '<?php endif;?>';
                            break;
                        case 'tag-self-close':
                            throw new TemplateException(__('if没有自闭合标签。示例：%1', '<if condition="$a>$b"><var>a</var><elseif condition="$b>$a"/><var>b</var><else/><var>a</var><var>b</var></if>'));
                        case 'tag-start':
                            $condition = $attributes['condition'];
                            $result    = "<?php if({$condition}):?>";
                            break;
                        case 'tag-end':
                            $result = '<?php endif;?>';
                            break;
                        default:
                    }
                    return $result;
                }],
            'empty'       => [
                'tag'      => 1,
                'tag-end'  => 1,
                'callback' => function ($tag_key, $config, $tag_data, $attributes) {
                    switch ($tag_key) {
                        // @empty{$name|<li>空的</li>}
                        case '@tag{}':
                        case '@tag()':
                            $content_arr = explode('|', $tag_data[1]);
                            return '<?php if(empty($this->getData(\'' . $content_arr[0] . '\')))echo \'' . $this->tmp_replace(trim($content_arr[1] ?? '')) . '\'?>';
                        case 'tag':
                            if (isset($attributes['name'])) {
                                throw new TemplateException(__('empty标签需要设置name属性！例如：<empty name="catalogs"><li>没有数据</li></empty>'));
                            }
                            return "<?php if(empty(\$this->getData('{$attributes['name']}'))): ?>";
                        case 'tag-end':
                            return "<?php endif; ?>";
                        default:
                            return '';
                    }
                }
            ],
            'elseif'      => [
                'attr'           => ['condition' => 1],
                'tag-self-close' => 1,
                'callback'       =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        $result = '';
                        switch ($tag_key) {
                            // @if{$a === 1=><li><var>$a</var></li>|$a===2=><li><var>$a</var></li>}
                            case '@tag{}':
                            case '@tag()':
                                throw new TemplateException(__('elseif没有@elseif()和@elseif{}用法。示例：%1', '<if condition="$a>$b"><var>a</var><elseif condition="$b>$a"/><var>b</var><else/><var>a</var><var>b</var></if>'));
                            case 'tag-self-close':
                                $condition = $attributes['condition'];
                                $result    = "<?php elseif({$condition}):?>";
                                break;
                            default:
                        }
                        return $result;
                    }],
            'else'        => [
                'tag-self-close' => 1,
                'callback'       =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        $result = '';
                        switch ($tag_key) {
                            // @if{$a === 1=><li><var>$a</var></li>|$a===2=><li><var>$a</var></li>}
                            case '@tag{}':
                            case '@tag()':
                                throw new TemplateException(__('elseif没有@elseif()和@elseif{}用法。示例：%1', '<if condition="$a>$b"><var>a</var><elseif condition="$b>$a"/><var>b</var><else/><var>a</var><var>b</var></if>'));
                            case 'tag-self-close':
                                $result = "<?php else:?>";
                                break;
                            default:
                        }
                        return $result;
                    }],
            'block'       => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => ObjectManager::getInstance(trim($tag_data[2])),
                            default => ObjectManager::getInstance(trim($tag_data[1]))
                        };
                    }
            ],
            'w-block'     => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => ObjectManager::getInstance(trim($tag_data[2])),
                            default => ObjectManager::getInstance(trim($tag_data[1]))
                        };
                    }
            ],
            'foreach'     => [
                'attr'      => ['name' => 1, 'key' => 0, 'item' => 0],
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  => function ($tag_key, $config, $tag_data, $attributes) {
                    switch ($tag_key) {
                        // @foreach{$name as $key=>$v|<li><var>$k</var>:<var>$v</var></li>}
                        case '@tag{}':
                        case '@tag()':
                            $content_arr = explode('|', $tag_data[1]);
                            return "<?php
                        foreach({$content_arr[0]}){
                        ?>
                            {$this->tmp_replace($content_arr[1]??'')}
                            <?php
                        }
                        ?>";
                        case 'tag-self-close':
                            throw new TemplateException(__('foreach没有自闭合标签。示例：%1', '<foreach name="catalogs" key="key" item="v"><li><var>name</var></li></foreach>'));
                        case 'tag-start':
                            if (!isset($attributes['item'])) {
                                $attributes['item'] = 'v';
                            }
                            if (!isset($attributes['name'])) {
                                throw new TemplateException(__('foreach标签需要指定要循环的变量name属性。例如：需要循环catalogs变量则%1', '<foreach name="catalogs" key="key" item="v"><li><var>name</var></li></foreach>'));
                            }
                            foreach ($attributes as $key => $attribute) {
                                if (!str_starts_with($attribute, '$')) {
                                    $attributes[$key] = '$' . $attribute;
                                }
                            }
                            $vars = $attributes['name'];
                            $k_i  = isset($attributes['key']) ? $attributes['key'] . ' => ' . $attributes['item'] : $attributes['item'];
                            return "<?php foreach($vars as $k_i):?>";
                        case 'tag-end':
                            return '<?php endforeach;?>';
                        default:
                            return '';
                    }
                }
            ],
            'w-foreach'   => [
                'attr'      => ['name' => 1, 'key' => 0, 'item' => 0],
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  => function ($tag_key, $config, $tag_data, $attributes) {
                    switch ($tag_key) {
                        // @foreach{$name as $key=>$v|<li><var>$k</var>:<var>$v</var></li>}
                        case '@tag{}':
                        case '@tag()':
                            $content_arr = explode('|', $tag_data[1]);
                            return "<?php
                        foreach({$content_arr[0]}){
                        ?>
                            {$this->tmp_replace($content_arr[1]??'')}
                            <?php
                        }
                        ?>";
                        case 'tag-self-close':
                            throw new TemplateException(__('foreach没有自闭合标签。示例：%1', '<foreach name="catalogs" key="key" item="v"><li><var>name</var></li></foreach>'));
                        case 'tag-start':
                            if (!isset($attributes['item'])) {
                                $attributes['item'] = 'v';
                            }
                            if (!isset($attributes['name'])) {
                                throw new TemplateException(__('foreach标签需要指定要循环的变量name属性。例如：需要循环catalogs变量则%1', '<foreach name="catalogs" key="key" item="v"><li><var>name</var></li></foreach>'));
                            }
                            foreach ($attributes as $key => $attribute) {
                                if (!str_starts_with($attribute, '$')) {
                                    $attributes[$key] = '$' . $attribute;
                                }
                            }
                            $vars = $attributes['name'];
                            $k_i  = isset($attributes['key']) ? $attributes['key'] . ' => ' . $attributes['item'] : $attributes['item'];
                            return "<?php foreach($vars as $k_i):?>";
                        case 'tag-end':
                            return '<?php endforeach;?>';
                        default:
                            return '';
                    }
                }
            ],
            'static'      => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2])),
                            default => $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))
                        };
                    }
            ],
            'w-static'    => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2])),
                            default => $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))
                        };
                    }
            ],
            'template'    => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => file_get_contents($this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($tag_data[2]))),
                            default => file_get_contents($this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($tag_data[1])))
                        };
                    }
            ],
            'w-template'  => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => file_get_contents($this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($tag_data[2]))),
                            default => file_get_contents($this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE, trim($tag_data[1])))
                        };
                    }
            ],
            'js'          => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => "<script {$tag_data[1]} src='{$this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2]))}'></script>",
                            default => "<script src='{$this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))}'></script>"
                        };
                    }
            ],
            'w-js'        => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => "<script {$tag_data[1]} src='{$this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2]))}'></script>",
                            default => "<script src='{$this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))}'></script>"
                        };
                    }
            ],
            'css'         => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => "<link {$tag_data[1]} href='{$this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2]))}'/>",
                            default => "<link href='{$this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))}'/>"
                        };
                    }
            ],
            'w-css'       => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => "<link {$tag_data[1]} href='{$this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[2]))}'/>",
                            default => "<link href='{$this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS, trim($tag_data[1]))}'/>"
                        };
                    }
            ],
            'lang'        => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'       => "<?=__('{$tag_data[2]}')?>",
                            'tag-start' => "<?=__('",
                            'tag-end'   => "')?>",
                            default     => "<?=__('{$tag_data[1]}')?>"
                        };
                    }
            ],
            'w-lang'      => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'       => "<?=__('{$tag_data[2]}')?>",
                            'tag-start' => "<?=__('",
                            'tag-end'   => "')?>",
                            default     => "<?=__('{$tag_data[1]}')?>"
                        };
                    }
            ],
            'url'         => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'       => "<?=\$this->getUrl('{$tag_data[2]}')?>",
                            'tag-start' => "<?=__('",
                            'tag-end'   => "')?>",
                            default     => "<?=\$this->getUrl('{$tag_data[1]}')?>"
                        };
                    }
            ],
            'w-url'       => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'       => "<?=\$this->getUrl('{$tag_data[2]}')?>",
                            'tag-start' => "<?=__('",
                            'tag-end'   => "')?>",
                            default     => "<?=\$this->getUrl('{$tag_data[1]}')?>"
                        };
                    }
            ],
            'admin-url'   => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'       => "<?=\$this->getAdminUrl('{$tag_data[2]}')?>",
                            'tag-start' => "<?=__('",
                            'tag-end'   => "')?>",
                            default     => "<?=\$this->getAdminUrl('{$tag_data[1]}')?>"
                        };
                    }
            ],
            'w-admin-url' => [
                'tag'       => 1,
                'tag-start' => 1,
                'tag-end'   => 1,
                'callback'  =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'       => "<?=\$this->getAdminUrl('{$tag_data[2]}')?>",
                            'tag-start' => "<?=__('",
                            'tag-end'   => "')?>",
                            default     => "<?=\$this->getAdminUrl('{$tag_data[1]}')?>"
                        };
                    }
            ],
            'hook'        => [
                'tag'      => 1,
                'callback' =>
                    function ($tag_key, $config, $tag_data, $attributes) {
                        return match ($tag_key) {
                            'tag'   => "<?=\$this->getHook('".trim($tag_data[2])."')?>",
                            default => "<?=\$this->getHook('".trim($tag_data[1])."')?>"
                        };
                    }
            ],
        ];
        /**@var EventsManager $event */
        $event = ObjectManager::getInstance(EventsManager::class);
        $data  = (new DataObject(['template' => $this, 'template_elements' => $template_elements]));
        $event->dispatch('Framework_Template::after_template_patterns_callback_map', ['data' => $data]);
        $template_elements = $data->getData('template_elements');

        foreach ($template_elements as $tag => $tag_configs) {
            $tag_patterns        = [
                'tag'            => '/<' . $tag . '([\s\S]*?)>([\s\S]*?)<\/' . $tag . '>/m',
                'tag-start'      => '/<' . $tag . '([\s\S]*?)>/m',
                'tag-end'        => '/<\/' . $tag . '>/m',
                'tag-self-close' => '/<' . $tag . '([\s\S]*?)\/>/m',
                '@tag()'         => '/\@' . $tag . '\(([\s\S]*?)\)/m',
                '@tag{}'         => '/\@' . $tag . '\{([\s\S]*?)\}/m',
            ];
            $tag_config_patterns = [];
            foreach ($tag_configs as $config_name => $tag_config) {
                if (str_starts_with($config_name, 'tag') && $tag_config) {
                    $tag_config_patterns[$config_name] = $tag_patterns[$config_name];
                }
            }
            # 默认匹配@tag()和@tag{}
            $tag_config_patterns['@tag()'] = $tag_patterns['@tag()'];
            $tag_config_patterns['@tag{}'] = $tag_patterns['@tag{}'];

            # 标签验证测试
//            if('var'===$tag){
//                foreach ($tag_config_patterns as &$tag_config_pattern) {
//                    $tag_config_pattern = htmlentities($tag_config_pattern);
//                }
//                p($tag_config_patterns);
//            }
            # 匹配处理
            $format_function = $tag_configs['callback'];
            foreach ($tag_config_patterns as $tag_key => $tag_pattern) {
                preg_match_all($tag_pattern, $content, $customTags, PREG_SET_ORDER);
                foreach ($customTags as $customTag) {
                    $originalTag   = $customTag[0];
                    $rawAttributes = $customTag[1] ?? '';
                    # 标签支持匹配->
                    if (!in_array($tag_key, ['@tag()', '@tag{}'])) {
                        $rawAttributes = rtrim($rawAttributes, '"');
                        $rawAttributes = rtrim($rawAttributes, '\'');
                        if (is_int(strrpos($rawAttributes, '\''))) {
                            $rawAttributes .= '\'';
                        }
                        if (is_int(strrpos($rawAttributes, '"'))) {
                            $rawAttributes .= '"';
                        }
                    }
                    $customTag[1]       = $rawAttributes;
                    $formatedAttributes = array();
                    # 兼容：属性值双引号
                    preg_match_all('/([^=]+)=\"([^\"]+)\"/', $rawAttributes, $attributes, PREG_SET_ORDER);
                    foreach ($attributes as $attribute) {
                        if (isset($attribute[2])) {
                            $formatedAttributes[trim($attribute[1])] = trim($attribute[2]);
                        }
                    }
                    # 兼容：属性值单引号
                    preg_match_all('/([^=]+)=\'([^\']+)\'/', $rawAttributes, $attributes, PREG_SET_ORDER);
                    foreach ($attributes as $attribute) {
                        if (isset($attribute[2])) {
                            $formatedAttributes[trim($attribute[1])] = trim($attribute[2]);
                        }
                    }
                    # 验证标签属性
                    $attrs = $tag_configs['attr'] ?? [];
                    if ($attrs && ('tar-start' === $tag_key || 'tag-self-close' === $tag_key)) {
                        $attributes_keys = array_keys($formatedAttributes);
                        foreach ($attrs as $attr => $required) {
                            if ($required && !in_array($attr, $attributes_keys)) {
                                $provide_attr = implode(',', $attributes_keys);
                                throw new TemplateException(__('%1:标签必须设置属性%2, 提供的属性：3% 文件：%4', [$tag, $attr, $provide_attr, $fileName]));
                            }
                        }
                    }
                    $content = str_replace($originalTag, $format_function($tag_key, $tag_configs, $customTag, $formatedAttributes), $content);
                }
            }
        }
        return $content;


        // 替换函数
        $patternsSynonymous     = function (string $template_element, &$replace_call_back) {
            return [
                'patterns'                   => [
                    '/<w-' . $template_element . '\s*([^>]*)\s*\/?<\/w-' . $template_element . '>/m',
                    //                    '/<w-' . $template_element . '\s*([^>]*)\s*\/?<\/w-' . $template_element . '>/m',
                    '/<' . $template_element . '\s*([^>]*)\s*\/?<\/' . $template_element . '>/m',
                    //                    '/<' . $template_element . '\s*([^>]*)\s*\/?<\/' . $template_element . '>/m',
                    //                    '/<w-' . $template_element . '([\s\S]*?)<\/w-' . $template_element . '>/m',
                    //                    '/<' . $template_element . '([\s\S]*?)<\/' . $template_element . '>/m',
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
            $back['origin']  = $back;
            $back['content'] = $content;
            $back['attrs']   = $attrs;
            $re_content      = '';
            if (isset($patterns_and_callbacks[$back[0]]) && $callback = $patterns_and_callbacks[$back[0]]) {
                $re_content = $callback($back);
            }
            /**@var EventsManager $event */
            $event = ObjectManager::getInstance(EventsManager::class);
            $data  = new DataObject(['back' => $back, 'content' => $re_content, 'object' => $this]);
            $event->dispatch('Framework_Template::after_template_replace', ['data' => $data]);
            return $data->getData('content');
        }, $content);
    }

    public function getUrl(string $path, array $params = [], bool $merge_query = true): string
    {
        return $this->_request->getUrl($path, $params, $merge_query);
    }

    public function getAdminUrl(string $path, array|bool $params = []): string
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

    /**
     * @throws \ReflectionException
     * @throws Exception
     * @throws Core
     */
    public function getHook(string $name)
    {
        /**@var Hooker $hooker */
        $hooker         = ObjectManager::getInstance(Hooker::class);
        $hookers        = $hooker->getHook($name);
        $hooker_content = '';
        foreach ($hookers as $module => $hooker_file) {
            $hooker_content .= "<!-- 来自模组 $module 的钩子实现代码 起-->" . $this->fetchTagHtml('hooks', $hooker_file) . "<!-- 来自模组 $module 的钩子实现代码 止-->";
        }
        return $hooker_content;
    }

    public function getRequest(): Request
    {
        return ObjectManager::getInstance(Request::class);
    }
}
