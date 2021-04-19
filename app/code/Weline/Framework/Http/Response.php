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
    private Response $instance;
    private array $headers = ['WELINE-LANG' => 'zh_Hans_CN'];

    private function __clone()
    {
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
    public function getInstance(): Response
    {
        if (!isset($this->instance)) {
            $this->instance = new self();
        }
        return $this->instance;
    }

    function setHeader(string $header_key, string $header_value)
    {
        $this->headers[$header_key] = $header_value;
        header("{$header_key}:{$header_value}");
        return $this;
    }

    function setHeaders(array $headers)
    {
        $this->headers = array_merge($this->headers, $headers);
        foreach ($this->headers as $header_key => $header_value) {
            header("{$header_key}:{$header_value}");
        }
        return $this;
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
        exit(include BP . '/404.html');
    }
}
