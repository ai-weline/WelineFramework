<?php

/*
 * 本文件由Aiweline编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Api\Rest\V1;

use Weline\Framework\App\Controller\BackendRestController;

class Index extends BackendRestController
{
    public function index()
    {
        $data = ['name' => '后台rest接口！', 'params' => $this->_request->getParams()];

        return $this->fetch($data, self::fetch_XML);
    }
}
