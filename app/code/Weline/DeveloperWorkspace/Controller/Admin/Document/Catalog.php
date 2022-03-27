<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\DeveloperWorkspace\Controller\Admin\Document;

use Weline\Framework\App\Exception;
use Weline\Framework\Exception\Core;

class Catalog extends \Weline\Admin\Controller\BaseController
{
    public function index()
    {
        $catalog  = $this->getCatalogModel();
        $catalogs = $catalog->select()->fetch();
        foreach ($catalogs as $catalog) {
            $level     = $catalog['level'] - 1;
            $level_str = $level ? str_repeat('-', $level) : '';
            $catalog->setName($level_str . $catalog['name']);
        }
        $this->assign('catalogs', $catalogs);
        return $this->fetch();
    }


    public function tree()
    {
        $trees = $this->getCatalogModel()->getTree();
        $trees = $this->processTrees($trees);
        return $this->fetchJson($trees);
    }

    private function processTrees(array &$trees)
    {
        foreach ($trees as &$tree) {
            $tree['text']       = '<a class="btn" href="' . $this->_url->build('dev/tool/admin/document/catalog', ['id' => $tree['id']]) . '">' . $tree['text'] . '</a>
<a class="btn btn-info pull-right" href="' . $this->_url->build('dev/tool/admin/document/catalog', ['id' => $tree['id']]) . '">修改</a>
<a class="btn btn-danger pull-right" href="' . $this->_url->build('dev/tool/admin/document/catalog/delete', ['id' => $tree['id']]) . '">' . __('删除') . '</a>
';
            $tree['selectable'] = true;
            $tree['state']      = [
                'checked'  => false,
                'disabled' => !$tree['is_active'],
                'expanded' => true,
                'selected' => false
            ];
            $tree['tags']       = ['available'];
            $tree['href']       = $this->_url->build('dev/tool/document/catalog', ['id' => $tree['id']]);
            if ($tree['nodes']) {
                $this->processTrees($tree['nodes']);
            }
        }
        return $trees;
    }

    /**
     * @throws \ReflectionException
     * @throws Exception
     * @throws Core
     */
    public function deleteDelete()
    {
        $catalogModel = $this->getCatalogModel()->load($this->_request->getParam('id'));
        if ($catalogModel->getId()) {
            try {
                $catalogModel->delete();
            } catch (\ReflectionException $e) {
            } catch (Exception $e) {
            } catch (Core $e) {
                $this->getMessageManager()->addException($e);
            }
        }
        $this->redirect($this->_url->build('dev/tool/admin/document/catalog'));
    }

    public function postPost()
    {
        $post      = $this->_request->getPost();
        $catalog   = $this->getCatalogModel();
        $pid_arr   = explode('-', $post['pid']);
        $pid       = array_shift($pid_arr);
        $pid_level = (int)array_shift($pid_arr);
        $level     = $pid_level;
        if ($level) {
            $level += 1;
        } else {
            $level = 1;
        }
        $post['name']      = trim($post['name']);
        if (empty($post['name'])) {
            $this->getMessageManager()->addError(__('目录名不能为空！'));
            $this->redirect($this->_url->build('dev/tool/admin/document/catalog'));
        }
        $post['pid']       = $pid;
        $post['level']     = $level;
        $post['is_active'] = 1;

        try {
            $catalog->save($post);
            $this->getMessageManager()->addSuccess(__('添加成功！'));
        } catch (\Exception $exception) {
            $this->getMessageManager()->addException($exception);
        }
        $this->redirect($this->_url->build('dev/tool/admin/document/catalog'));
    }

    private function getCatalogModel(): \Weline\DeveloperWorkspace\Model\Document\Catalog
    {
        return $this->_objectManager::getInstance(\Weline\DeveloperWorkspace\Model\Document\Catalog::class);
    }
}
