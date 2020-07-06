<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/5
 * 时间：17:45
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\App\Controller;


use M\Framework\App\Exception;
use M\Framework\Controller\Core;
use M\Framework\Http\Request;
use M\Framework\View\Data\DataInterface;
use M\Framework\View\Template;
use ReflectionObject;

class FrontendController extends Core
{
    protected Template $_template;

    /**
     * Controller 初始函数...
     * @throws Exception
     * @throws \ReflectionException
     */
    function __construct()
    {
        parent::__construct();
        $this->_template = new Template($this->_request, $this->getViewBaseDir());
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param array|string $tpl_var
     * @param array|string $value
     */
    protected function assign($tpl_var, $value = null)
    {
        if (is_string($tpl_var)) {
            $this->_template->assign($tpl_var, $value);
        }
        if (is_array($tpl_var)) {
            foreach ($tpl_var as $key => $item) {
                $this->_template->assign($key, $item);
            }
        }
    }

    /**
     * @DESC         |获取模板渲染
     *
     * 参数区：
     *
     * @param string $fileName
     * @return bool
     * @throws Exception
     */
    protected function fetch(string $fileName = null)
    {

        if ($fileName == null) {
            $parent_call_info = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
            $fileNameArr = explode(\M\Framework\Controller\Data\DataInterface::dir, $parent_call_info['class']);
            $fileName = trim(array_pop($fileNameArr), '\\') . DIRECTORY_SEPARATOR . $parent_call_info['function'];
        };
        return $this->_template->fetch($fileName);

    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return Request
     */
    protected function getRequest()
    {
        return $this->_request;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return Template
     */
    protected function getTemplate()
    {
        return $this->_template;
    }

    /**
     * @DESC         |按照类型获取view目录
     *
     * 参数区：
     *
     * @return string
     */
    protected function getViewBaseDir()
    {
        $reflect = new ReflectionObject($this);
        $ctl_dir_reflect_arr = explode(self::dir, $reflect->getFileName());
        $module_dir = array_shift($ctl_dir_reflect_arr);
        return $module_dir . DataInterface::dir . DIRECTORY_SEPARATOR;
    }
}