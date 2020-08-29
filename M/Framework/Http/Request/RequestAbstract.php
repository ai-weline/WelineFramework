<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/23
 * 时间：22:46
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Http\Request;


use M\Framework\App\Env;
use M\Framework\Controller\Data\DataInterface;
use M\Framework\Http\Response;

abstract class RequestAbstract
{
    const HEADER = 'header';

    const MOBILE_DEVICE_HEADERS = [
        'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc',
        'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic',
        'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry',
        'meizu', 'android', 'netfront', 'symbian', 'ucweb',
        'windowsce', 'palm', 'operamini', 'operamobi', 'openwave',
        'nexusone', 'cldc', 'midp', 'wap', 'mobile'
    ];

    private string $area_router;

    function __construct()
    {
        $url_arr = explode('/', trim($this->getUrl(), '/'));
        $this->area_router = array_shift($url_arr);
    }

    /**
     * @DESC         |获取请求区域
     *
     * 参数区：
     *
     * @return string
     */
    function getRequestArea()
    {

        switch ($this->area_router) {
            case Env::getInstance()->getConfig('admin', 'admin'):
                return DataInterface::type_pc_BACKEND;
            case Env::getInstance()->getConfig('api_admin', 'api_admin'):
                return DataInterface::type_api_REST_BACKEND;
            case '':
            default:
                return DataInterface::type_pc_FRONTEND;
        }
    }

    /**
     * @DESC         |获取
     *
     * 参数区：
     *
     * @return mixed|string
     */
    function getAreaRouter()
    {
        return $this->area_router;
    }

    /**
     * @DESC         |方法描述
     *
     * @Author       秋枫雁飞
     * @Email        aiweline@qq.com
     * @Forum        https://bbs.aiweline.com
     * @Description  此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
     *
     * 参数区：
     *
     * @param string $key
     * @return string
     */
    function getServer(string $key = null): string
    {
        $filter = RequestFilter::getInstance();
        $filter->init();
        if ($key) {
            switch ($key) {
                case self::HEADER:
                    $params = [];
                    foreach ($_SERVER as $name => $value) {
                        if (substr($name, 0, 5) == 'HTTP_') {
                            $params[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                        }
                    }
                    break;
                default:
                    $params = isset($_SERVER[$key]) ? $_SERVER[$key] : '';
            }
        } else {
            $params = $_SERVER;
        }
        return $params;
    }

    /**
     * @DESC         |请求方法
     *
     * 参数区：
     *
     * @return string
     */
    function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * @DESC         |是否手机设备
     *
     * 参数区：
     *
     * @return bool
     */
    function isMobile(): bool
    {
        //如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        //如via信息有wap一定是移动设备，但是部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            //找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        //判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            //从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', self::MOBILE_DEVICE_HEADERS) . ")/i",
                strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }

        }
        //协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            //如果只支持wml并且不支持html那一定是app
            //如果支持wml和html但是wml在html之前则是app
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false)
                && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false ||
                    (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml')
                        < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))
                )) {
                return true;
            }
        }
        return false;
    }


    function getUri(): string
    {
        return $this->getServer('REQUEST_URI');
    }

    function getUrl(): string
    {
        $uri = $this->getUri();
        $url_exp = explode('?', $uri);
        return array_shift($url_exp);
    }

    function getBaseUrl(): string
    {
        $uri = $this->getUri();
        $url_exp = explode('?', $uri);
        return $this->getBaseHost() . array_shift($url_exp);
    }

    function getBaseUri(): string
    {
        $uri = $this->getUri();
        $url_exp = explode('?', $uri);
        return $this->getBaseHost() . array_shift($url_exp);
    }

    function getBaseHost(): string
    {
        return $this->getServer('REQUEST_SCHEME') . '://' . $this->getServer('HTTP_HOST');
    }

    /**
     * @DESC         |获取响应类
     *
     * 参数区：
     *
     * @return Response
     */
    function getResponse(): Response
    {
        return Response::getInstance();
    }
}