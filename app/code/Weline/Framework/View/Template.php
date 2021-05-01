<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\View;

use think\db\Fetch;
use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\Cache\CacheInterface;
use Weline\Framework\Controller\PcController;
use Weline\Framework\DataObject\DataObject;
use Weline\Framework\Event\EventsManager;
use Weline\Framework\Exception\Core;
use Weline\Framework\Http\Request;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\View\Cache\ViewCache;
use Weline\Framework\View\Data\DataInterface;

class Template
{
    const file_ext = '.phtml';

    protected Request $_request;

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

    private EventsManager $eventsManager;

    /**
     * @var CacheInterface 缓存
     */
    private CacheInterface $viewCache;

    public function __construct(PcController $controller)
    {
        $this->controller = $controller;
    }

//FIXME 实现拓展第三方自定义模板引擎
    public function __init()
    {
        $this->_request = $this->controller->getRequest();
        $this->view_dir = $this->controller->getViewBaseDir();
        $this->eventsManager = ObjectManager::getInstance(EventsManager::class);
        $this->viewCache = ObjectManager::getInstance(ViewCache::class)->create();
        $this->vars['title'] = $this->_request->getModuleName();
        $this->statics_dir = $this->getViewDir(DataInterface::view_STATICS_DIR);
        $this->template_dir = $this->getViewDir(DataInterface::view_TEMPLATE_DIR);
        $this->compile_dir = $this->getViewDir(DataInterface::view_TEMPLATE_COMPILE_DIR);
    }

    /**
     * @DESC         |按照类型获取view目录
     *
     * 参数区：
     *
     * @param string $type
     * @return string
     */
    private function getViewDir(string $type = '')
    {
        switch ($type) {
            case DataInterface::dir_type_TEMPLATE:
                $path = $this->view_dir . DataInterface::view_TEMPLATE_DIR;

                break;
            case DataInterface::dir_type_TEMPLATE_COMPILE:
                $path = $this->view_dir . DataInterface::view_TEMPLATE_COMPILE_DIR;

                break;
            case DataInterface::dir_type_STATICS:
                $cache_key = 'getViewDir' . $type;
                if (!DEV && $cache_static_dir = $this->viewCache->get($cache_key)) {
                    return $cache_static_dir;
                }
                $path = $this->view_dir . DataInterface::view_STATICS_DIR . DIRECTORY_SEPARATOR;
                $theme = Env::getInstance()->getConfig('theme', Env::default_theme_DATA);

                if (!DEV) {
                    $path = str_replace(APP_PATH, PUB . 'static' . DIRECTORY_SEPARATOR . $theme['path'] . DIRECTORY_SEPARATOR, $path);
                    $this->viewCache->set($cache_key, $path);
                }

                break;
            default:
                $path = $this->view_dir;

                break;
        }
        $path = $path . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) {
            mkdir($path, 0770, true);
        }

        return $path;
    }

    /**
     * @DESC         |获取视图文件
     *
     * 参数区：
     *
     * @param $filepath
     * @return string
     */
    public function getViewFile($filepath)
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
     * @param null|string $key
     * @return mixed|null
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->vars;
        }

        return isset($this->vars[$key]) ? $this->vars[$key] : null;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param string $real_path
     * @return string
     */
    private function getUrlPath(string $real_path): string
    {
        $explode_str = PUB;
        if (DEV) {
            $explode_str = APP_PATH;
        }
        $dir_arr = explode($explode_str, $real_path);

        return DIRECTORY_SEPARATOR . array_pop($dir_arr);
    }

    /**
     * @DESC         |模板中变量分配调用的方法
     *
     * 参数区：
     *
     * @param string $tpl_var
     * @param null $value
     */
    public function assign(string $tpl_var, $value = null)
    {
        $this->vars[$tpl_var] = $value;

        return $this;
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
        // 解析模板路由
        $fileName = str_replace('/', DIRECTORY_SEPARATOR, $fileName);
        $file_name_dir_arr = explode(DIRECTORY_SEPARATOR, $fileName);
        $file_dir = null;
        $file_name = null;

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

        // 检测模板文件：如果文件名有后缀 则直接到view下面读取。没有说明是默认
        if ($file_ext) {
            $tplFile = $this->view_dir . $fileName;
        } else {
            $tplFile = $this->template_dir . $fileName . self::file_ext;
        }
        $tplFile = $this->fetchFile($tplFile);
        if (!file_exists($tplFile)) {
            if (DEV) {
                throw new Exception('模板文件：' . $tplFile . '不存在！');
            }

            return false;
        }

        // 检测目录是否存在,不存在则建立
        $baseComFileDir = $this->compile_dir . ($file_dir ? $file_dir : '');
        if (!is_dir($baseComFileDir)) {
            mkdir($baseComFileDir, 0770, true);
        }

        //定义编译合成的文件 加了前缀 和路径 和后缀名.phtml
        if ($file_ext) {
            $comFileName = $baseComFileDir . 'com_' . $file_name;
        } else {
            $comFileName = $baseComFileDir . 'com_' . $file_name . self::file_ext;
        }
        $comFileName = $this->fetchFile($comFileName);

        if (DEV || !file_exists($comFileName) || filemtime($comFileName) < filemtime($tplFile)) {
            //如果缓存文件不存在则 编译 或者文件修改了也编译
            $repContent = $this->tmp_replace(file_get_contents($tplFile));//得到模板文件 并替换占位符 并得到替换后的文件
            file_put_contents($comFileName, $repContent);//将替换后的文件写入定义的缓存文件中
        }

        //包含编译后的文件
        require $comFileName;
    }

    /**
     * @DESC         | 取得对应的文件
     *
     * 参数区：
     * @param string $filename
     * @throws \Weline\Framework\Exception\Core
     */
    protected function fetchFile(string $filename)
    {
        if (!DEV && $cache_filename = $this->viewCache->get($filename)) {
            return $cache_filename;
        }
        /*---------观察者模式 检测文件是否被继承-----------*/
        $fileData = new DataObject(['filename' => $filename, 'type' => 'compile']);
        $this->eventsManager->dispatch(
            'Framework_View_event::template_fetch_file',
            ['object' => $this, 'data' => $fileData]
        );
        $event_filename = $fileData->getData('filename');
        if (!DEV) {
            $this->viewCache->set($filename, $event_filename);
        }

        return $event_filename;
    }

    /**
     * @DESC         |替换模板中的占位符
     *
     * 参数区：
     *
     * @param $content
     * @return string|string[]|null
     */
    private function tmp_replace($content)
    {
        // <php></php>标签 TODO 静态资源
        $replaces = [
            '@static' => $this->getUrlPath($this->statics_dir),
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
            '/\@template (.*)/',
            '/\@view (.*)/',
            '/\@p\((.+)\)/',
            '/\@if\((.*)\)\{(.*)\}/',
            '/\@foreach\((.*)\)\{(.*)\}/',
        ];
        $replacement = [
            '<?php echo $this->vars["${1}"]; ?>',
            '<?php ${1} ?>',
            '<?php include(trim("${1}")); ?>',
            '<?php $this->fetch(trim("templates/${1}")); ?>',
            '<?php $this->fetch(trim("${1}")); ?>',
            '<?php p(isset($this->getData("${1}"))??${1}); ?>',
            '<?php if(${1})echo addslashes("${2}"); ?>',
            "<?php 
            \$func_data = \"\${1}\";
            \$func_data_arr = explode(\" as \",\$func_data);
            if(count(\$func_data_arr)!=2) throw new \Weline\Framework\App\Exception('foreach模板语法使用错误！提示：forum as v,k,{渲染元素} 示例用法：@foreach(forum as v,k,<li>键{k}:值{v}</li>)');
            \$foreach_data = \$this->getData(array_shift(\$func_data_arr));
            \$_k_v_loop_arr = explode('=>',array_shift(\$func_data_arr));
            if(count(\$_k_v_loop_arr)!=2)throw new \Weline\Framework\App\Exception('foreach模板语法使用错误！提示：v,k 示例用法：@foreach(forum as v,k,<li>键{k}:值{v}</li>)');
            \$foreach_loop_str = '\${2}';
            \$k_name = '$'.\$_k_v_loop_arr[0];
            \$v_name = '$'.\$_k_v_loop_arr[1];
            foreach (\$foreach_data as \$k_name => \$v_name){
                \$foreach_loop_str_tmp = \$foreach_loop_str;
                foreach(array_unique(getStringBetweenContents(\$foreach_loop_str_tmp,'{','}')) as \$t_k_t=>\$t_v_t){  
                    \$t_v_t_arr = explode('.',\$t_v_t);
                    \$t_v_t_key = isset(\$t_v_t_arr[1])?\$t_v_t_arr[1]:false;
                    if(\$t_v_t_key){
                        \$foreach_loop_str_tmp = str_replace('{'.\$t_v_t.'}',\$v_name[\$t_v_t_key],\$foreach_loop_str);
                    }else{
                        throw new \Weline\Framework\App\Exception('foreach模板语法使用错误！提示：v,k 示例用法：@foreach(forum as v,k,<li>键{k}:值{v}</li>)');
                    }
                };
                echo \$foreach_loop_str_tmp;
            }
            ?>",
        ];
        // TODO 完善foreach模板
        $content = preg_replace($pattern, $replacement, $content);

//        $foreach_str_arr = explode(',',$foreach_str);

//        if(count($foreach_str_arr) != 3) throw new Exception('foreach模板语法使用错误！示例用法：@foreach(data,<li>键{$k}:值{$v}</li>,$k:$v)');
//        $data = $this->getData($foreach_str_arr[0]);
//        $loop_str= $foreach_str_arr[1];
//        $loop_k_v= explode(':',$foreach_str_arr[3]);
//        if(count($loop_k_v) != 2) throw new Exception('foreach模板语法使用错误！请使用 $k:$v 形式作为第三个参数，示例用法：@foreach(data,<li>键{$k}:值{$v}</li>,$k:$v)');
//        foreach($data as $loop_k_v[0]=>$loop_k_v[1]){
//            echo $loop_str;
//        }
        return $content;
    }

    public function getUrl(string $path)
    {
        return $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/' . $path;
    }
}
