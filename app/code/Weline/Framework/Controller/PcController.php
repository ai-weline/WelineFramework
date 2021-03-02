<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Controller;

use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;
use Weline\Framework\View\Data\DataInterface;
use Weline\Framework\View\Template;
use ReflectionObject;

class PcController extends Core
{
    private Template $_template;

    /**
     * 设置
     * @param Template $template
     * @return PcController
     */
    public function setTemplate(Template $template)
    {
        $this->_template = $template;

        return $this;
    }

    /**
     * @DESC         |获取模板数据
     *
     * 参数区：
     *
     * @param string $key
     * @return array|mixed|null
     */
    public function getData(string $key = null)
    {
        return $this->getTemplate()->getData($key);
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @return $this|Template
     */
    public function getTemplate(): Template
    {
        if (! isset($this->_template)) {
            $this->_template = new Template($this->getRequest(), $this->getViewBaseDir());
        }

        return $this->_template;
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     * @param array|string $tpl_var
     * @param array|string $value
     * @return PcController
     */
    protected function assign($tpl_var, $value = null)
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
     * @param string $fileName
     * @throws Exception
     * @return bool
     */
    protected function fetch(string $fileName = null)
    {
        if ($fileName === null) {
            $parent_call_info = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
            $fileNameArr      = explode(\Weline\Framework\Controller\Data\DataInterface::dir, $parent_call_info['class']);
            $fileName         = trim(array_pop($fileNameArr), '\\') . DIRECTORY_SEPARATOR . $parent_call_info['function'];
        }

        return $this->getTemplate()->fetch($fileName);
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
        $reflect             = new ReflectionObject($this);
        $filename            = $reflect->getFileName();
        $filename            = str_replace(Env::GENERATED_DIR, 'app', $filename);
        $ctl_dir_reflect_arr = explode(self::dir, $filename);
        $module_dir          = array_shift($ctl_dir_reflect_arr);
        $module_dir          = $module_dir . DataInterface::dir . DIRECTORY_SEPARATOR;
        if (! is_dir($module_dir)) {
            mkdir($module_dir, 0775, true);
        }

        return $module_dir;
    }
}
