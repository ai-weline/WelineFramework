<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Http;

// TODO 完善返回

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
        if (! isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**]
     * @DESC         |无路由
     *
     * 参数区：
     *
     */
    public function noRouter()
    {
        http_response_code(404);
        @header('http/1.1 404 not found');
        @header('status: 404 not found');
        include BP . '/404.html';
        exit(0);
    }
}
