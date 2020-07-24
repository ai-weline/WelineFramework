<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/17
 * 时间：21:42
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\NewsSource\Api\Rest\V1;


use Aiweline\NewsSource\Model\AiwelineNews;
use Aiweline\NewsSource\Service\News;
use M\Framework\App\Controller\FrontendRestController;

class Source extends FrontendRestController
{
    function post()
    {
        if ('k51bjpx499uka816ud9awok0ytarvwxz' != $this->_request->getAuth()) $this->_request->getResponse()->noRouter();
        if ('aiweline_news' != $this->_request->getHeader('target')) $this->_request->getResponse()->noRouter();
        // 必须参数
        $need_params = array(
            'title' => null,
            'content' => null,
            'abstract' => null,
            'author' => null,
            'category' => null,
            'source' => null,
            'pushtime' => null,
        );
        $response_params = array(
            'id' => '',
            'target' => 'news',
            'desc' => '',
            'error' => ''
        );
        // 参数
        $params = $this->_request->getParams();
        $params = array_intersect_key($params, $need_params);
        if (count($params) != count($need_params)) {
            $response_params['error'] = '参数不完整！';
            return $this->fetch($response_params);
        };
        $result = (new News())->add($params);
        $response_params['id'] = $result['id'];
        if (!$result['code']) {
            $response_params['error'] = $result['msg'];
        } else {
            $response_params['desc'] = '发布成功！';
        }
        return $this->fetch($response_params);
    }
}