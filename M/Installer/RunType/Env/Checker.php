<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/7
 * 时间：21:36
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Installer\RunType\Env;


use M\Framework\App\Exception;
use M\Framework\Output\Cli\Printing;
use M\Installer\Helper\Data;

class Checker
{
    const type_MODULES = 'modules';
    const type_FUNCTIONS = 'functions';
    protected Data $helper;
    private Printing $printer;


    protected array $canCheck = ['modules', 'functions'];
    protected array $module = [];
    protected array $method = [];
    private bool $isCli;

    function __construct()
    {
        $this->helper = new Data();
        $this->isCli = (PHP_SAPI === 'cli');
        $this->printer = new Printing();

    }

    function run()
    {
        // 循环检测
        foreach ($this->helper->getCheckEnv() as $type => $data) {
            $this->setNeed($type, $data);
        }
        return $this->check();
    }

    function setNeed(string $type, $value)
    {
        if (!in_array($type, $this->canCheck)) {
            $this->canCheck[] = $type;
        };
        if (is_array($value)) {
            $this->$type = $value;
        } else {
            $this->$type[] = $value;
        }
        return $this;
    }

    function check(): array
    {
        $tmp = [];
        $hasErr = false;
        foreach ($this->canCheck as $item) {
            if (is_array($this->$item)) {
                switch ($item) {
                    case self::type_FUNCTIONS:
                        $disable_functions = ini_get('disable_functions');
                        $disable_functions = explode(',', $disable_functions);
                        foreach ($this->$item as $i) {
                            if (in_array($i, $disable_functions)) {
                                $hasErr = true;
                                $key = str_pad($item . '---' . $i, 45, '-', STR_PAD_BOTH);
                                $value = str_pad('✖', 10, " ", STR_PAD_BOTH);
                                if ($this->isCli) {
                                    $this->printer->error($key . '=>' . $value);
                                }
                                $tmp[$key] = $value;
                            } else {
                                $key = str_pad($item . '---' . $i, 45, '-', STR_PAD_BOTH);
                                $value = str_pad('✔', 10, " ", STR_PAD_BOTH);
                                if ($this->isCli) {
                                    $this->printer->success($key . '=>' . $value);
                                }
                                $tmp[$key] = $value;
                            }
                        }
                        break;
                    case self::type_MODULES:
                        $modules = get_loaded_extensions();
                        foreach ($this->$item as $needModule) {
                            if (in_array($needModule, $modules)) {
                                $key = str_pad($item . '---' . $needModule, 45, '-', STR_PAD_BOTH);
                                $value = str_pad('✔', 10, " ", STR_PAD_BOTH);
                                if ($this->isCli) {
                                    $this->printer->success($key . '=>' . $value);
                                }
                                $tmp[$key] = $value;
                            } else {
                                $hasErr = true;
                                $key = str_pad($item . '---' . $needModule, 45, '-', STR_PAD_BOTH);
                                $value = str_pad('✖', 10, " ", STR_PAD_BOTH);
                                if ($this->isCli) {
                                    $this->printer->error($key . '=>' . $value);
                                }
                                $tmp[$key] = $value;
                            }
                        }
                        break;
                    default:
                        $tmp['ERR: ' . $item] = '不存在的检测类型！（✖）';
                }
            }
        }
        return ['data' => $tmp, 'hasErr' => $hasErr, 'msg' => '-------  环境初始化检测中...  -------'];
    }
}