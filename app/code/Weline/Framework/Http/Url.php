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

    public function __construct(
        Request $request
    )
    {
        $this->request = $request;
    }

    public function getBackendApiUrl(string $path = '', array $params = [], bool $merge_params = true): string
    {
        if ($path) {
            $url = $this->request->getBaseHost() . '/' . Env::getInstance()->getConfig('api_admin') . '/' . $path;
        } else {
            $url = $this->request->getBaseUrl();
        }
        return $this->extractedUrl($params, $merge_params, $url);
    }

    public function getUrl(string $path = '', array $params = [], bool $merge_params = false): string
    {
        if ($path) {
            $url = $this->request->getBaseHost() . '/' . ltrim($path, '/');
        } else {
            $url = $this->request->getBaseUrl();
        }
        return $this->extractedUrl($params, $merge_params, $url);
    }

    function getBackendUrl(string $path = '', array $params = [], bool $merge_params = true): string
    {
        if ($path) {
            $url = $this->request->getBaseHost() . '/' . Env::getInstance()->getConfig('admin') . (('/' === $path) ? '' : '/' . $path);
        } else {
            $url = $this->request->getBaseUrl();
        }
        return $this->extractedUrl($params, $merge_params, $url);
    }

    /**
     * @DESC          # 提取Url
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/2/8 23:27
     * 参数区：
     *
     * @param array  $params
     * @param bool   $merge_params
     * @param string $url
     *
     * @return string
     */
    public function extractedUrl(array $params, bool $merge_params, string $url): string
    {
        if ($params) {
            if ($merge_params) {
                $url .= '?' . http_build_query(array_merge($this->request->getGet(), $params));
            } else {
                $url .= '?' . http_build_query($params);
            }
        } else {
            $url .= ($this->request->getGet() && $merge_params) ? '?' . http_build_query($this->request->getGet()) : '';
        }
        return $url;
    }
}
