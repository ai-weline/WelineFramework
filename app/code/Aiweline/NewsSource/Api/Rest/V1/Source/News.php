<?php

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\NewsSource\Api\Rest\V1\Source;

use Aiweline\NewsSource\Model\AiwelineNews;
use Weline\Framework\App\Controller\FrontendRestController;

class News extends FrontendRestController
{
    public function getList()
    {
        if ('k51bjpx499uka816ud9awok0ytarvwxz' !== $this->_request->getAuth()) {
            $this->_request->getResponse()->noRouter();
        }
        if ('aiweline_news' !== $this->_request->getHeader('target')) {
            $this->_request->getResponse()->noRouter();
        }
        $news_model = new AiwelineNews();
        $page       = $this->_request->getParam('page') ?? 1;
        $pageSize   = $this->_request->getParam('pageSize') ?? 10;

        $news = $news_model->order('create_time', 'desc')->page($page, $pageSize)->select();

        return $this->fetch($news);
    }

    /**
     * @DESC         |方法描述
     *
     * 参数区：
     */
    public function get()
    {
        $news_model = new AiwelineNews();
        $id         = $this->_request->getParam('id');
        if (null === $id) {
            $this->error('资源ID不存在！');
        }
        $news = $news_model->with(['content', 'category', 'source'])->find($id);

        return $this->fetch($news);
    }
}
