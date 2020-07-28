<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/21
 * 时间：18:47
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Http;


use M\Framework\Http\Request\RequestFilter;

class Request extends Request\RequestAbstract implements RequestInterface
{
    private static Request $instance;
    /**
     * @var RequestFilter
     */
    protected RequestFilter $_filter;

    private string $module_name;
    private string $module_path;

    /**
     * @return string|string[]
     */
    public function getModuleName()
    {
        return $this->module_name;
    }

    /**
     * 设置
     * @param string|string[] $module_name
     */
    public function setModuleName($module_name): void
    {
        $this->module_name = $module_name;
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
     */
    public function setModulePath(string $module_path): void
    {
        $this->module_path = $module_path;
    }

    private function __clone()
    {
    }

    private function __construct($module_path)
    {
        $this->module_path = $module_path;
        $this->module_name = str_replace('\\', '_', $module_path);
        $this->_filter = RequestFilter::getInstance();
    }

    final static function getInstance(string $module_path): self
    {
        if (!isset(self::$instance)) self::$instance = new self($module_path);
        return self::$instance;
    }

    function getHeader(string $key = null)
    {
        if (empty($key)) return $this->getServer(self::HEADER);
        return $this->getServer('HTTP_' . strtoupper($key));
    }

    function getParam(string $key)
    {
        parse_str($this->getServer('QUERY_STRING'), $params);
        array_shift($params);
        return isset($params[$key]) ? $params[$key] : null;
    }

    function getParams()
    {
        parse_str($this->getServer('QUERY_STRING'), $params);
        array_shift($params);
        $params = array_merge($params, $_POST);
        $params = array_merge($params, $_GET);
        return $params;
    }


    function getBodyParam($key)
    {
        $params = $this->getBodyParams();
        return isset($params[$key]) ? $params[$key] : null;
    }

    function getBodyParams()
    {
        $params = file_get_contents('php://input');
        if ($this->getContentType() === self::CONTENT_TYPE['json']) {
            $params = json_decode($params, true);
        }
        return $params;
    }

    function isPost(): bool
    {
        return $this->getMethod() === self::POST;
    }

    function isGet(): bool
    {
        return $this->getMethod() === self::GET;
    }

    function isPut(): bool
    {
        return $this->getMethod() === self::PUT;
    }

    function isDelete(): bool
    {
        return $this->getMethod() === self::DELETE;
    }

    public function getContentType(): string
    {
        return $this->getServer('CONTENT_TYPE');
    }

    function getAuth(string $auth_type = 'bearer')
    {
        switch ($auth_type) {
            case self::auth_TYPE_BEARER:
                return str_replace('Bearer ', '', $this->getHeader('Authorization'));
            case self::auth_TYPE_BASIC_AUTH:
                return array('USER' => $this->getServer('PHP_AUTH_USER'), 'PW' => $this->getServer('PHP_AUTH_PW'));
            default:
                return null;
        }
    }

    function getApiKey(string $key): string
    {
        return $this->getHeader($key);
    }
}