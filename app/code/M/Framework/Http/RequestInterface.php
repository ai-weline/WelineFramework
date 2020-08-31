<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/21
 * 时间：2:09
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Http;


interface RequestInterface
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const UPDATE = 'UPDATE';

    const METHODS = [
        self::GET,
        self::POST,
        self::PUT,
        self::DELETE,
        self::UPDATE,
    ];

    const CONTENT_TYPE = [
        'json' => 'application/json',
        'xml' => 'application/xml',
    ];

    const auth_TYPE_BEARER = 'bearer';
    const auth_TYPE_BASIC_AUTH = 'basic';

    /**
     * @DESC         |获取服务server
     *
     * 参数区：
     *
     * @return array
     */
    function getServer();

    /**
     * @DESC         |读取uri
     *
     * 参数区：
     *
     * @return string
     */
    function getUri(): string;

    /**
     * @DESC         |获取内容类型
     *
     * 参数区：
     *
     * @return string
     */
    public function getContentType();

    /**
     * @DESC         |获取请求头
     *
     * 参数区：
     *
     * @param string|null $key
     * @return array|null
     */
    function getHeader(string $key = null);

    /**
     * @DESC         |获取Auth验证,默认方式 Bearer
     *
     * 参数区：
     *
     * @param string $auth_type
     * @return array|string|null
     */
    function getAuth(string $auth_type = self::auth_TYPE_BEARER);

    /**
     * @DESC         |API_KEY验证
     *
     * 参数区：
     *
     * @param string $key
     * @return string
     */
    function getApiKey(string $key):string;

    /**
     * @DESC         |获取传统键值对参数
     *
     * 参数区：
     *
     * @param string $key
     * @return mixed
     */
    function getParam(string $key);

    /**
     * @DESC         |获取全部参数
     *
     * 参数区：
     *
     * @return mixed
     */
    function getParams();


    /**
     * @DESC         |获取body体json对应key参数
     *
     * 参数区：
     *
     * @param $key
     * @return mixed
     */
    function getBodyParam($key);

    /**
     * @DESC         |获取body参数,json自动转化为数组
     *
     * 参数区：
     *
     * @return mixed
     */
    function getBodyParams();

    /**
     * @DESC         |是否post方法
     *
     * 参数区：
     *
     * @return bool
     */
    function isPost(): bool;

    /**
     * @DESC         |是否GET请求
     *
     * 参数区：
     *
     * @return bool
     */
    function isGet(): bool;

    /**
     * @DESC         |是否PUT请求
     *
     * 参数区：
     *
     * @return bool
     */
    function isPut(): bool;

    /**
     * @DESC         |是否DELETE请求
     *
     * 参数区：
     *
     * @return bool
     */
    function isDelete(): bool;

    /**
     * @DESC         |获取请求方法
     *
     * 参数区：
     *
     * @return string
     */
    function getMethod(): string;

    /**
     * @DESC         |获取路径
     *
     * 参数区：
     *
     * @return string
     */
    function getBaseUri(): string;

    /**
     * @DESC         |获取基础URL
     *
     * 参数区：
     *
     * @return string
     */
    function getBaseHost(): string;
}