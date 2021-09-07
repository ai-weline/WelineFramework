<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Http;

// TODO 完善返回


use JetBrains\PhpStorm\NoReturn;

class Response implements ResponseInterface
{
    private Response $instance;

    private array $headers = ['WELINE-USER-LANG' => 'zh_Hans_CN'];

    public function setHeader(string $header_key, string $header_value): static
    {
        $this->headers[$header_key] = $header_value;
        header("{$header_key}:{$header_value}");

        return $this;
    }

    public function setHeaders(array $headers): static
    {
        $this->headers = array_merge($this->headers, $headers);
        foreach ($this->headers as $header_key => $header_value) {
            header("{$header_key}:{$header_value}");
        }

        return $this;
    }

    /**
     * @DESC          # 无路由
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/9/7 23:06
     * 参数区：
     */
    #[NoReturn] public function noRouter():void
    {
        http_response_code(404);
        @header('http/2.0 404 not found');
        @header('status: 404 not found');
        exit(include BP . '/404.html');
    }
}
