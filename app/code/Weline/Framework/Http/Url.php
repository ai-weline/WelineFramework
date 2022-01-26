<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Http;

use Weline\Framework\App\Env;
use Weline\Framework\Session\Session;

class Url implements UrlInterface
{
    protected Request $request;

    function __construct(
        Request $request
    )
    {
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    function build(string $path='', array $params = []): string
    {
        if(empty($path)){
            return $this->get_url();
        }
        $pre = $this->request->getBaseHost() . '/';
        if ($this->request->isBackend()) {
            $pre .= Env::getInstance()->getConfig('admin') . '/';
        } elseif ($this->request->isApiBackend()) {
            $pre .= Env::getInstance()->getConfig('api_admin') . '/';
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

    function get_url() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
        $path_info = $_SERVER['PATH_INFO'] ?? '';
        $relate_url = $_SERVER['REQUEST_URI'] ?? $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
        return $sys_protocal.($_SERVER['HTTP_HOST'] ?? '').$relate_url;
    }
}