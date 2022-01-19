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
    protected Session $session;
    protected Request $request;

    function __construct(
        Session $session,
        Request $request
    )
    {
        $this->session = $session;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    function build(string $path): string
    {
        $pre = $this->request->getBaseHost().'/';
        if ($this->session->isBackend()) {
            $pre .= '/'.Env::getInstance()->getConfig('admin') . '/';
        }
        return $pre . $path;
    }
}