<?php
/**
 * 文件信息
 * 作者：邹万才
 * 网名：秋风雁飞(可以百度看看)
 * 网站：www.aiweline.com/bbs.aiweline.com
 * 工具：PhpStorm
 * 日期：2020/7/23
 * 时间：16:24
 * 描述：此文件源码由Aiweline（秋枫雁飞）开发，请勿随意修改源码！
 */

namespace Aiweline\NewsSource\Api\Rest\V1\Source;


use Aiweline\NewsSource\Model\AiwelineNews;
use Aiweline\NewsSource\Setup\Install;
use M\Framework\App\Controller\FrontendRestController;

class News extends FrontendRestController
{
    function getList()
    {
        if ('k51bjpx499uka816ud9awok0ytarvwxz' != $this->_request->getAuth()) $this->_request->getResponse()->noRouter();
        if ('aiweline_news' != $this->_request->getHeader('target')) $this->_request->getResponse()->noRouter();
        $news_model = new AiwelineNews();
        $page = $this->_request->getParam('page') ?? 1;
        $pageSize = $this->_request->getParam('pageSize') ?? 10;

        $news = $news_model->order('create_time', 'desc')->page($page, $pageSize)->select();
        return $this->fetch($news);
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     *
     */
    function get()
    {
        $news_model = new AiwelineNews();
        $id = $this->_request->getParam('id');
        if (null == $id) $this->error('资源ID不存在！');
        $news = $news_model->with(['content','category','source'])->find($id);
        return $this->fetch($news);
    }
}