<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Http;

use Weline\Framework\Http\Request\RequestFilter;

class Request extends Request\RequestAbstract implements RequestInterface
{
    private static Request $instance;

    /**
     * @var RequestFilter
     */
    protected RequestFilter $_filter;

    private string $module_name;

    private string $module_path;

    public function __init()
    {
        /**
         * 重载方法
         */
        parent::__init();
        $this->module_path = '';
        $this->module_name = str_replace('\\', '_', $this->module_path);
    }

    /**
     * 设置
     * @param string $module_name
     * @return Request
     */
    public function setModuleName($module_name): Request
    {
        $this->module_name = $module_name;

        return $this;
    }

    /**
     * @return string
     */
    public function getModulePath(): string
    {
        return $this->module_path;
    }

    /**
     * 设置
     * @param string $module_path
     * @return Request
     */
    public function setModulePath(string $module_path): Request
    {
        $this->module_path = $module_path;

        return $this;
    }

    private function __clone()
    {
    }

    final public static function getInstance(string $module_path): self
    {
        if (! isset(self::$instance)) {
            self::$instance = (new self())->setModulePath($module_path);
        }

        return self::$instance;
    }

    public function getHeader(string $key = null)
    {
        if (empty($key)) {
            return $this->getServer(self::HEADER);
        }

        return $this->getServer('HTTP_' . strtoupper($key));
    }

    public function getParam(string $key)
    {
        parse_str($this->getServer('QUERY_STRING'), $params);
        array_shift($params);
        $params = array_merge($params, $_POST);
        $params = array_merge($params, $_GET);

        return $params[$key] ?? null;
    }

    public function getParams()
    {
        parse_str($this->getServer('QUERY_STRING'), $params);
        array_shift($params);
        $params = array_merge($params, $_POST);
        $params = array_merge($params, $_GET);

        return $params;
    }

    public function getBodyParam($key)
    {
        $params = $this->getBodyParams();

        return $params[$key] ?? null;
    }

    public function getBodyParams()
    {
        $params = file_get_contents('php://input');
        if ($this->getContentType() === self::CONTENT_TYPE['json']) {
            $params = json_decode($params, true);
        }

        return $params;
    }

    public function isPost(): bool
    {
        return $this->getMethod() === self::POST;
    }

    public function isGet(): bool
    {
        return $this->getMethod() === self::GET;
    }

    public function isPut(): bool
    {
        return $this->getMethod() === self::PUT;
    }

    public function isDelete(): bool
    {
        return $this->getMethod() === self::DELETE;
    }

    public function getContentType(): string
    {
        return $this->getServer('CONTENT_TYPE');
    }

    public function getAuth(string $auth_type = 'bearer')
    {
        switch ($auth_type) {
            case self::auth_TYPE_BEARER:
                return str_replace('Bearer ', '', $this->getHeader('Authorization'));
            case self::auth_TYPE_BASIC_AUTH:
                return ['USER' => $this->getServer('PHP_AUTH_USER'), 'PW' => $this->getServer('PHP_AUTH_PW')];
            default:
                return null;
        }
    }

    public function getApiKey(string $key): string
    {
        return $this->getHeader($key);
    }
}
