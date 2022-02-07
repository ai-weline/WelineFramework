<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Http;

use Weline\Framework\Http\Request\RequestFilter;
use Weline\Framework\Manager\ObjectManager;

class Request extends Request\RequestAbstract implements RequestInterface
{
    private static Request $instance;

    /**
     * @var RequestFilter
     */
    protected RequestFilter $_filter;

    private string $module_name = '';

    function __init()
    {
        parent::__init();
        $this->setData($this->getParams());
    }

    /**
     * @return string
     */
    public function getModulePath(): string
    {
        return $this->getRouterData('module_path');
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
        if ($result = $this->getData($key)) {
            return $result;
        }
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
        if (is_int(strpos($this->getContentType(), self::CONTENT_TYPE['json']))) {
            $params = json_decode($params, true);
        }

        return $params;
    }

    function getPost(string $key = '')
    {
        $data = $_POST;
        if ($key) {
            if (isset($data[$key]) && $data = $data[$key]) {
                return $data;
            } else {
                return null;
            }
        }
        return $data;
    }

    function getGet(string $key = '')
    {
        $data = $_GET;
        if ($key) {
            if (isset($data[$key]) && $data = $data[$key]) {
                return $data;
            } else {
                return null;
            }
        }
        return $data;
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

    public function getReferer(): string
    {
        return $this->getServer('HTTP_REFERER');
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

    public function getModuleName(): string
    {
        if ($module_name = parent::getModuleName()) {
            return $module_name;
        } else {
            return $this->module_name;
        }
    }

    function clientIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } else if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } else if (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }

    public function getUrl(string $path = '', array|bool $params = []): string
    {
        if ($path) {
            $url = $this->getBaseHost() . '/' . $path;
        } else {
            $url = $this->getBaseUrl();
        }
        if (empty($params)) return $url;
        if (is_array($params)) {
            $url .= '?' . http_build_query($params);
        } else if (is_bool($params)) {
            $url .= '?' . http_build_query($this->getGet());
        }
        return $url;
    }
}
