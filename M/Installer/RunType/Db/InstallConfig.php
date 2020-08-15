<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/8/7
 * 时间：22:06
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Installer\RunType\Db;


use M\Framework\App\Env;
use M\Framework\App\Exception;
use M\Framework\Http\Request;
use M\Framework\Setup\Data\Setup as DataSetup;
use M\Installer\Helper\Data;

class InstallConfig
{
    protected Data $helper;
    /**
     * @var Request
     */
    private Request $request;

    function __construct()
    {
        $this->helper = new Data();
        $this->request = Request::getInstance('M\\Installer');
    }

    function run(): array
    {
        $params = $this->request->getParams();
        unset($params['action']);
        $params['type'] = 'mysql';

        $tables = $this->helper->getDbTables();
        $hasErr = false;
        $db = array(
            'default' => $params['type'],
            'connections' =>
                array(
                    'mysql' => $params
                ),
        );
        // 数据库信息初始化
        Env::getInstance()->setConfig('db', $db);
        $db_conf = Env::getInstance()->getDbConfig();
        if(empty($db_conf)){
            $hasErr = true;
        }
        return ['data' => $db, 'hasErr' => $hasErr, 'msg' => '-------  数据库配置安装...  -------'];
    }
}