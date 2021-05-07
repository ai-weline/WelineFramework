<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\NewsSource\Api\Rest\V1;

use Aiweline\NewsSource\Service\News;
use Weline\Framework\App\Controller\FrontendRestController;

class Source extends FrontendRestController
{
    public function post()
    {
        if ('k51bjpx499uka816ud9awok0ytarvwxz' !== $this->_request->getAuth()) {
            $this->_request->getResponse()->noRouter();
        }
        if ('aiweline_news' !== $this->_request->getHeader('target')) {
            $this->_request->getResponse()->noRouter();
        }
        // 必须参数
        $need_params = [
            'title'    => null,
            'content'  => null,
            'abstract' => null,
            'author'   => null,
            'category' => null,
            'source'   => null,
            'pushtime' => null,
        ];
        $response_params = [
            'id'     => '',
            'target' => 'news',
            'desc'   => '',
            'error'  => '',
        ];
        // 参数
        $params = $this->_request->getParams();
        $params = array_intersect_key($params, $need_params);
        if (count($params) !== count($need_params)) {
            $response_params['error'] = '参数不完整！';

            return $this->fetch($response_params);
        }
        $result                = (new News())->add($params);
        $response_params['id'] = $result['id'];
        if (! $result['code']) {
            $response_params['error'] = $result['msg'];
        } else {
            $response_params['desc'] = '发布成功！';
        }

        return $this->fetch($response_params);
    }
}
