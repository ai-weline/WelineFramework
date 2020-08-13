<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/6/21
 * 时间：19:14
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace M\Framework\Http;

// TODO 完善返回
use M\Framework\Http\Request\BaseRequest;

class Response implements ResponseInterface
{
    private static Response $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    private function __construct()
    {
    }

    /**
     * @DESC         |获取实例
     *
     * 参数区：
     *
     * @return Response
     */
    public static function getInstance(): Response
    {
        if (!isset(self::$instance)) self::$instance = new self();
        return self::$instance;
    }

    /**]
     * @DESC         |无路由
     *
     * 参数区：
     *
     */
    function noRouter()
    {
        http_response_code(404);
        @header("http/1.1 404 not found");
        @header("status: 404 not found");
        include(BP."/404.html");
        exit(0);
    }
}