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

class Template
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

    private function __construct()
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
        $this->_request = ObjectManager::getInstance(Request::class);
        $this->view_dir = BP . $this->_request->getRouterData('module_path') . DataInterface::dir . DIRECTORY_SEPARATOR;
        $this->vars['title'] = $this->_request->getModuleName();

        $this->theme = Env::getInstance()->getConfig('theme', Env::default_theme_DATA);
        $this->eventsManager = ObjectManager::getInstance(EventsManager::class);
        $this->viewCache = ObjectManager::getInstance(ViewCache::class)->create();

        $this->statics_dir = $this->getViewDir(DataInterface::view_STATICS_DIR);
        $this->template_dir = $this->getViewDir(DataInterface::view_TEMPLATE_DIR);
        $this->compile_dir = $this->getViewDir(DataInterface::view_TEMPLATE_COMPILE_DIR);
        return $this;
    }

    /**
     * @DESC          # 获取form_key
     *
     * @AUTH  秋枫雁飞
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
     * @return string
     * @throws Core
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
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string|null $key
     * @return mixed|null
     */
    public function getData(string $key = null): mixed
    {
        if ($key === null) {
            return $this->vars;
        }

        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }


    /**
     * @DESC         |模板中变量分配调用的方法
     *
     * 参数区：
     *
     * @param string $tpl_var
     * @param null $value
     * @return Template
     */
    public function assign(string $tpl_var, mixed $value = null): static
    {
        $this->vars[$tpl_var] = $value;

        return $this;
    }


    function getFetchFile(string $fileName): string
    {
        $comFileName_cache_key = $this->view_dir . $fileName . '_comFileName';
        $tplFile_cache_key = $this->view_dir . $fileName . '_tplFile';
        $comFileName = '';
        $tplFile = '';
        if (PROD) {
            $comFileName = $this->viewCache->get($comFileName_cache_key);
            $tplFile = $this->viewCache->get($tplFile_cache_key);
        }
        # 测试
//        file_put_contents(__DIR__ . '/test.txt', $comFileName . PHP_EOL, FILE_APPEND);
        // 编译文件不存在的时候 重新对文件进行处理 防止每次都处理
        if (empty($comFileName) || empty($tplFile)) {
            // 解析模板路由
            $fileName = str_replace('/', DIRECTORY_SEPARATOR, $fileName);
            $file_name_dir_arr = explode(DIRECTORY_SEPARATOR, $fileName);
            $file_dir = '';
            $file_name = '';

            // 如果给的文件名字有路径
            if (count($file_name_dir_arr) > 1) {
                $file_name = array_pop($file_name_dir_arr);
                $file_dir = implode(DIRECTORY_SEPARATOR, $file_name_dir_arr);
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
            # 生产模式缓存
            if (PROD) {
                $this->viewCache->set($comFileName_cache_key, $comFileName);
                $this->viewCache->set($tplFile_cache_key, $tplFile);
            };
        }
        # 测试
//        file_put_contents(__DIR__ . '/test.txt', $comFileName . PHP_EOL, FILE_APPEND);
        if (is_int(strpos($comFileName, '\\'))) str_replace('\\', DIRECTORY_SEPARATOR, $comFileName);
        if (is_int(strpos($comFileName, '//'))) str_replace('/', DIRECTORY_SEPARATOR, $comFileName);
        # 检测编译文件，如果不符合条件则重新进行文件编译
        if (DEV || !file_exists($comFileName) || filemtime($comFileName) < filemtime($tplFile)) {
            //如果缓存文件不存在则 编译 或者文件修改了也编译
            $repContent = $this->tmp_replace(file_get_contents($tplFile));//得到模板文件 并替换占位符 并得到替换后的文件
            file_put_contents($comFileName, $repContent);//将替换后的文件写入定义的缓存文件中
        }
        return $comFileName;
    }

    /**
     * @DESC         |调用模板显示
     *
     * 参数区：
     *
     * @param string $fileName
     * @return bool|void
     * @throws Core
     */
    public function fetch(string $fileName)
    {
        $comFileName = $this->getFetchFile($fileName);
        # 是否显示模板路径
        //包含编译后的文件
        require $comFileName;

    }

    /**
     * @DESC         |替换模板中的占位符
     *
     * 参数区：
     *
     * @param $content
     * @return string|string[]|null
     */
    private function tmp_replace($content): array|string|null
    {
        $static_url_path = $this->getUrlPath($this->statics_dir);
        $replaces = [
            '__static__' => $static_url_path,
            '__STATIC__' => $static_url_path,
            '<php>' => '<?php ',
            '</php>' => '?>',
        ];
        foreach ($replaces as $tag => $replace) {
            $content = str_replace($tag, $replace, $content);
        }
        $pattern = [
            '/\<\!--\s*\$([a-zA-Z]*)\s*--\>/',
            '/\@\{(.*)\}/',
            '/\@include (.*)/',
            '/\@block\((.*)\)/',
            '/\@template\((.*)\)/',
            '/\@static\((.*)\)/',
            '/\@view\((.*)\)/',
            '/\@p\((.+)\)/',
            /*'/\@if\((.*)\)\{(.*)\}/',
            '/\@foreach\((.*)\)\:(.*)foreach\;/m',//TODO 完成foreach多行模式兼容*/
        ];
        $replacement = [
            '<?php echo $this->vars["${1}"]; ?>',
            '<?php ${1} ?>',
            '<?php include(trim("${1}")); ?>',
            '<?php echo $this->getBlock(trim("${1}"));//打印Block块对象 ?>',
            '<?php echo $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_TEMPLATE,trim("${1}"));// 读取资源文件 ?>',
            '<?php echo $this->fetchTagSource(\Weline\Framework\View\Data\DataInterface::dir_type_STATICS,trim("${1}"));// 读取资源文件 ?>',
            '<?php $this->fetch(trim("${1}")); ?>',
            '<?php p(isset($this->getData("${1}"))?:${1}); ?>',
            /*'<?php if(${1})echo addslashes("${2}"); ?>',
            "<?php
            \$func_data = \"\${1}\";
            \$func_data_arr = explode(\" as \",\$func_data);
            if(count(\$func_data_arr)!=2) throw new \Weline\Framework\App\Exception('foreach模板语法使用错误！提示：forum as v,k,{渲染元素} 示例用法：@foreach(forum as v,k,<li>键{k}:值{v}</li>)');
            \$foreach_data = \$this->getData(trim(array_shift(\$func_data_arr)));
            \$_k_v_loop_arr = explode('=>',trim(array_shift(\$func_data_arr)));
            if(count(\$_k_v_loop_arr)!=2)throw new \Weline\Framework\App\Exception('foreach模板语法使用错误！提示：v,k 示例用法：@foreach(forum as v,k,<li>键{k}:值{v}</li>)');
            \$foreach_loop_str = '\${2}';
            \$k_name = '$'.trim(\$_k_v_loop_arr[0]);
            \$v_name = '$'.trim(\$_k_v_loop_arr[1]);
            if(is_array(\$foreach_data))
            {
                foreach (\$foreach_data as \$k_name => \$v_name){
                    \$foreach_loop_str_tmp = \$foreach_loop_str;
                    foreach(array_unique(getStringBetweenContents(\$foreach_loop_str_tmp,'{','}')) as \$t_k_t=>\$t_v_t){  
                        \$t_v_t_arr = explode('.',\$t_v_t);
                        \$t_v_t_key = isset(\$t_v_t_arr[1])?trim(\$t_v_t_arr[1]):false;
                        if(\$t_v_t_key){
                            \$foreach_loop_str_tmp = str_replace('{'.\$t_v_t.'}',\$v_name[\$t_v_t_key],\$foreach_loop_str);
                        }else{
                            throw new \Weline\Framework\App\Exception('foreach模板语法使用错误！提示：v,k 示例用法：@foreach(forum as v,k,<li>键{k}:值{v}</li>)');
                        }   
                    }
                }
                echo \$foreach_loop_str_tmp;
            }
            ?>",*/
        ];
        //        $foreach_str_arr = explode(',',$foreach_str);

//        if(count($foreach_str_arr) != 3) throw new Exception('foreach模板语法使用错误！示例用法：@foreach(data,<li>键{$k}:值{$v}</li>,$k:$v)');
//        $data = $this->getData($foreach_str_arr[0]);
//        $loop_str= $foreach_str_arr[1];
//        $loop_k_v= explode(':',$foreach_str_arr[3]);
//        if(count($loop_k_v) != 2) throw new Exception('foreach模板语法使用错误！请使用 $k:$v 形式作为第三个参数，示例用法：@foreach(data,<li>键{$k}:值{$v}</li>,$k:$v)');
//        foreach($data as $loop_k_v[0]=>$loop_k_v[1]){
//            echo $loop_str;
//        }
        return preg_replace($pattern, $replacement, $content);
    }

    public function getUrl(string $path, array|bool $params = []): string
    {
        $path_url = $this->_request->getUrl($path);
        if (empty($params)) {
            return $path_url;
        }
        if (is_array($params)) {
            return $path_url . '?' . http_build_query($params);
        }
        if (is_bool($params) && $params) {
            return $path_url . '?' . http_build_query($this->_request->getGet());
        }
        return $path_url;
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
        $path = rtrim($pre . $path,'/');
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
