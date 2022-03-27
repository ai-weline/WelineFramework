<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\DeveloperWorkspace\Controller\Admin;

use Weline\DeveloperWorkspace\Model\Document\Catalog;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\System\File\Uploader;

class Document extends \Weline\Framework\App\Controller\BackendController
{
    public function index()
    {
        /**@var Catalog $catalog */
        $catalog  = ObjectManager::getInstance(Catalog::class);
        $catalogs = $catalog->pagination(
            intval($this->_request->getParam('page', 1)),
            intval($this->_request->getParam('pageSize', 10)),
            $this->_request->getParams()
        )
                            ->select()
                            ->fetch();
        $this->assign('catalogs', $catalogs);
        $this->assign('columns', $catalog->columns());
        return $this->fetch();
    }

    public function add()
    {
        $this->assign('a', 1);
        return $this->fetch();
    }

    public function postPost()
    {
        /**@var \Weline\DeveloperWorkspace\Model\Document $document*/
        $document = ObjectManager::getInstance(\Weline\DeveloperWorkspace\Model\Document::class);
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
