<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/27
 * 时间：11:50
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\View;


use M\Framework\App\Etc;
use M\Framework\App\Exception;
use M\Framework\Http\Request;
use M\Framework\View\Data\DataInterface;

class Template
{

    const file_ext = '.phtml';

    protected Request $_request;
    /**
     * @var string 指定模板目录
     */
    private string $template_dir;

    /**
     * @var string 编译后的目录
     */
    private string $compile_dir;
    /**
     * @var string 静态文件目录
     */
    private string $statics_dir;
    /**
     * @var string 静态文件目录
     */
    private string $view_dir;

    /**
     * @var array 读取模板中所有变量的数组
     */
    private array $arr_var = array();

    /**
     * Template 初始函数...
     * @param Request $request
     * @param string $view_dir
     * @param string $statics_dir
     * @param string $template_dir
     * @param string $compile_dir
     * @throws Exception
     */
    public function __construct(Request &$request, string $view_dir)

    {
        $this->_request = $request;
        $this->view_dir = $view_dir;
        $this->statics_dir = $this->getViewDir(DataInterface::view_STATICS_DIR);
        $this->template_dir = $this->getViewDir(DataInterface::view_TEMPLATE_DIR);
        $this->compile_dir = $this->getViewDir(DataInterface::view_TEMPLATE_COMPILE_DIR);
    }

    /**
     * @DESC         |获取视图文件
     *
     * 参数区：
     *
     * @param $filepath
     */
    function getViewFile($filepath)
    {
        $path = $this->view_dir  . $filepath;
        if (!file_exists($path) && DEBUG) throw new Exception('文件不存在！位置：' . $path);
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
    function getData($key = null)
    {
        if ($key == null) return $this->arr_var;
        return isset($this->arr_var[$key]) ? $this->arr_var[$key] : null;
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
//        $base = $this->_request->getBaseHost() . DIRECTORY_SEPARATOR;
        $dir_arr = explode(APP_PATH,$real_path);
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
        $this->arr_var[$tpl_var] = $value;

    }

    /**
     * @DESC         |调用模板显示
     *
     * 参数区：
     *
     * @param string $fileName
     * @return bool|void
     * @throws Exception
     */
    public function fetch(string $fileName)
    {
        // 解析模板路由
        $file_name_dir_arr = explode(DIRECTORY_SEPARATOR, $fileName);
        $file_dir = null;
        $file_name = null;
        if (count($file_name_dir_arr) > 1) {
            $file_name = array_pop($file_name_dir_arr);
            $file_dir = implode(DIRECTORY_SEPARATOR, $file_name_dir_arr);
            if ($file_dir) $file_dir .= DIRECTORY_SEPARATOR;
        }
        // 检测模板文件
        $tplFile = $this->template_dir . $fileName . self::file_ext;
        if (!file_exists($tplFile)) {
            if (Etc::getInstance()->isDebug()) throw new Exception('模板文件：' . $tplFile . '不存在！');
            return false;
        }
        //定义编译合成的文件 加了前缀 和路径 和后缀名.phtml
        $baseComFileDir = $this->compile_dir . ($file_dir ? $file_dir : '');
        if (!is_dir($baseComFileDir)) mkdir($baseComFileDir, 0770);// 检测目录是否存在,不存在则建立
        $comFileName = $baseComFileDir . "com_" . $file_name . self::file_ext;
        if (!file_exists($comFileName) || filemtime($comFileName) < filemtime($tplFile)) {
            //如果缓存文件不存在则 编译 或者文件修改了也编译
            $repContent = $this->tmp_replace(file_get_contents($tplFile));//得到模板文件 并替换占位符 并得到替换后的文件
            file_put_contents($comFileName, $repContent);//将替换后的文件写入定义的缓存文件中
        }
        //包含编译后的文件
        define('__STATIC__', $this->getUrlPath($this->statics_dir));
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
    private function tmp_replace($content)
    {
        $pattern = array(
            '/\<\!--\s*\$([a-zA-Z]*)\s*--\>/i'
        );

        $replacement = array(
            '<?php echo $this->arr_var["${1}"]; ?>'
        );
        $content = preg_replace($pattern, $replacement, $content);
        // <php></php>标签
        $patterns = array(
            '<php>'=>'<?php ',
            '</php>'=>'?>'
        );
        foreach ($patterns as $tag=>$replace) {
            $content = str_replace($tag, $replace, $content);
        }
        return $content;
    }

    /**
     * @DESC         |按照类型获取view目录
     *
     * 参数区：
     *
     * @param string $type
     * @return string
     */
    protected function getViewDir(string $type = '')
    {
        switch ($type) {
            case DataInterface::dir_type_TEMPLATE:
                $path = $this->view_dir . DataInterface::view_TEMPLATE_DIR;
                break;
            case DataInterface::dir_type_TEMPLATE_COMPILE:
                $path = $this->view_dir . DataInterface::view_TEMPLATE_COMPILE_DIR;
                break;
            case DataInterface::dir_type_STATICS:
                $path = $this->view_dir . DataInterface::view_STATICS_DIR;
                break;
            default:
                $path = $this->view_dir;
                break;
        }
        $path = $path . DIRECTORY_SEPARATOR;
        if (!is_dir($path)) mkdir($path, 0770,true);
        return $path;
    }

}