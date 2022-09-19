<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2022/9/18 12:44:40
 */

namespace Weline\UrlManager\Controller\Backend;

use Weline\Framework\App\Exception;
use Weline\Framework\Database\Exception\ModelException;
use Weline\Framework\Manager\ObjectManager;
use Weline\UrlManager\Model\UrlManager;
use Weline\UrlManager\Model\UrlRewrite;

class Rewriter extends \Weline\Framework\App\Controller\BackendController
{
    function get()
    {
        /**@var UrlRewrite $urlRewriteModel*/
        $urlRewriteModel = ObjectManager::getInstance(UrlRewrite::class);
        $rewrites = $urlRewriteModel->pagination()->select()->fetch();
        $this->assign('rewrites',$rewrites->getItems());
        $this->assign('pagination',$rewrites->getPagination());
        return $this->fetch();
    }

    function post()
    {
        $data = $this->request->getPost();
        if (!isset($data['path'])) {
            $data['path'] = $data['origin_path'];
        }
        /**@var UrlRewrite $urlRewriter */
        $urlRewriter = ObjectManager::getInstance(UrlRewrite::class);
        $urlRewriter->setData($data);
        try {
            $urlRewriter->save();
        } catch (\ReflectionException|Exception|ModelException $e) {
            $this->getMessageManager()->addError($e->getMessage());
        }
        $this->getMessageManager()->addSuccess(__('重写成功！'));
//        $this->redirect($this->request->getAdminUrl('/url-manager/backend/url/listing'));
        $this->redirect($this->_url->build('url-manager/backend/url/rewriter'));
    }

    function getForm()
    {
        $uri_identify = $this->request->getGet('identify');
        /**@var UrlManager $urlManager */
        $urlManager = ObjectManager::getInstance(UrlManager::class);
        $url        = $urlManager->where($urlManager::fields_IDENTIFY, $uri_identify)
                                 ->fields('main_table.*,ur.rewrite as rewrite_path')
                                 ->joinModel(UrlRewrite::class, 'ur', 'main_table.identify=ur.url_identify', 'left')
                                 ->find()->fetch();
        $this->assign('url', $url);
        return $this->fetch();
    }
}