<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\DeveloperWorkspace\Controller\Admin;

use Weline\Admin\Model\AdminUser;
use Weline\DeveloperWorkspace\Model\Document\Catalog;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\System\File\Uploader;

class Document extends \Weline\Framework\App\Controller\BackendController
{
    public function index()
    {
        /**@var \Weline\DeveloperWorkspace\Model\Document $documentModel */
        $documentModel = ObjectManager::getInstance(\Weline\DeveloperWorkspace\Model\Document::class);
        $documents     = $documentModel->pagination(
            intval($this->_request->getParam('page', 1)),
            intval($this->_request->getParam('pageSize', 10)),
            $this->_request->getParams()
        )
                                       ->select()
                                       ->fetch();
        $this->assign('documents', $documents);
        $this->assign('pagination', $documentModel->getPagination());
        $this->assign('columns', $documentModel->columns());
        return $this->fetch();
    }

    public function add()
    {
        // 分类
        /**@var Catalog $catalogModel */
        $catalogModel = ObjectManager::getInstance(Catalog::class);
        $catalogs     = $catalogModel->select()->fetch();
        foreach ($catalogs as &$catalog) {
            $name    = $catalog->getName();
            $level   = (int)$catalog->getLevel() - 1;
            $name    = ($level ? str_repeat('-', $level) : '') . $name;
            $catalog = $catalog->setData('name', $name);
        }
        $this->assign('catalogs', $catalogs);
        # 作者
        /**@var AdminUser $adminUserModel */
        $adminUserModel = ObjectManager::getInstance(AdminUser::class);
        $this->assign('users', $adminUserModel->select()->fetch());
        return $this->fetch();
    }

    public function postPost()
    {
        # 保存
        /**@var \Weline\DeveloperWorkspace\Model\Document $documentModel */
        $documentModel = ObjectManager::getInstance(\Weline\DeveloperWorkspace\Model\Document::class);
        try {
            $documentModel->save($this->_request->getPost());
            $this->getMessageManager()->addSuccess('添加文档成功！ID:' . $documentModel->getId());
        } catch (\Exception $exception) {
            $this->exception($exception);
        }
        $this->redirect($this->_url->build('dev/tool/admin/document'));
    }

    public function upload()
    {
        $uploader = new Uploader();
        p($_FILES);
//        $filename =$_FILES[];
//        $uploader->setModuleName('Weline_DeveloperWorkspace')
//        ->saveFile($file_tmp, $filename);
    }
}
