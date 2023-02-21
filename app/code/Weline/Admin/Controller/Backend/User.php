<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/1/29 22:12:42
 */

namespace Weline\Admin\Controller\Backend;

use Weline\Acl\Model\Role;
use Weline\Backend\Model\Backend\Acl\UserRole;
use Weline\Backend\Model\BackendUser;
use Weline\Framework\Manager\ObjectManager;

#[\Weline\Framework\Acl\Acl('Weline_Admin::system_user_listing', '用户管理', '管理后台用户','')]
class User extends \Weline\Framework\App\Controller\BackendController
{
    private \Weline\Backend\Model\BackendUser $backendUser;

    function __construct(
        \Weline\Backend\Model\BackendUser $backendUser
    )
    {
        $this->backendUser = $backendUser->clearData();
    }

    #[\Weline\Framework\Acl\Acl('Weline_Admin::system_user_listing', '管理员列表', '','查看管理后台用户列表')]
    function listing()
    {
        if ($search = $this->request->getGet('search')) {
            $aclModelFields = implode(',', $this->backendUser->getModelFields());
            $this->backendUser->where('CONCAT_WS(' . $aclModelFields . ')', '%' . $search . '%', 'like');
        }
        $users = $this->backendUser->pagination()->select()->fetch();
        $this->assign('users', $users->getItems());
        $this->assign('pagination', $users->getPagination());
        return $this->fetch();
    }

    #[\Weline\Framework\Acl\Acl('Weline_Admin::system_user_add', '管理员添加界面','', '添加管理员界面访问')]
    function getAdd()
    {
        $this->assign('user',  $this->backendUser->clearData()->setData($this->session->getData('user'))->getData());
        $this->assign('action', $this->request->getUrlBuilder()->getBackendUrl('*/backend/user/add', $this->request->getGet()));
        return $this->fetch('form');
    }

    #[\Weline\Framework\Acl\Acl('Weline_Admin::system_user_edit', '管理员修改界面','', '修改管理员界面访问')]
    function getEdit()
    {
        $user = clone $this->backendUser->clear()->load($this->request->getGet('id'));
        $this->assign('edit_user', $user);
        $this->assign('action', $this->request->getUrlBuilder()->getBackendUrl('*/backend/user/edit', $this->request->getGet()));
        return $this->fetch('form');
    }

    #[\Weline\Framework\Acl\Acl('Weline_Admin::system_user_edit_post', '管理员修改请求','', '修改管理员请求')]
    function postEdit()
    {
        try {
            $this->backendUser->clearData()
                              ->setId($this->request->getPost('user_id'))
                              ->setUsername($this->request->getPost('username'))
                              ->setEmail($this->request->getPost('email'))
                              ->setPassword($this->request->getPost('password'))
                              ->save(true);
            $this->getMessageManager()->addSuccess(__('修改成功！'));
            $this->redirect('*/backend/user/edit', ['id' => $this->backendUser->getId()]);
        } catch (\Exception $exception) {
            $this->session->setData('user', $this->request->getPost());
            $this->getMessageManager()->addWarning(__('修改失败！'));
            if (DEV) $this->getMessageManager()->addException($exception);
            $this->redirect('*/backend/user/add');
        }
    }

    #[\Weline\Framework\Acl\Acl('Weline_Admin::system_user_add_post', '管理员添加请求','', '请求添加管理员')]
    function postAdd()
    {
        try {
            $this->backendUser->clearData()->setUsername($this->request->getPost('username'))
                              ->setEmail($this->request->getPost('email'))
                              ->setPassword(trim($this->request->getPost('password')))
                              ->save(true);
            $this->getMessageManager()->addSuccess(__('添加成功！'));
            $this->redirect('*/backend/user/edit', ['id' => $this->backendUser->getId()]);
        } catch (\Exception $exception) {
            $this->session->setData('user', $this->request->getPost());
            $this->getMessageManager()->addWarning(__('添加失败！'));
            if (DEV) $this->getMessageManager()->addException($exception);
            $this->redirect('*/backend/user/add');
        }
    }

    #[\Weline\Framework\Acl\Acl('Weline_Admin::system_assign_role', '管理员角色归配','', '将管理员分配到角色')]
    function getAssignRole()
    {
        /**@var Role $role */
        $role = ObjectManager::getInstance(Role::class);
        /**@var BackendUser $backendUser */
        $backendUser = ObjectManager::getInstance(BackendUser::class);
        $users       = $backendUser
            ->joinModel(UserRole::class, 'ur', 'main_table.user_id=ur.user_id')
            ->pagination()
            ->select()
            ->fetch();
//        p($users->getLastSql());
        $this->assign('current_user', $this->session->getLoginUser($backendUser::class));
        $this->assign('users', $users->getItems());
        $this->assign('pagination', $users->getPagination());
        $this->assign('roles', $role->select()->fetch()->getItems());
        return $this->fetch('assign_role');
    }

    #[\Weline\Framework\Acl\Acl('Weline_Admin::system_assign_role_post', '管理员角色归配请求','', '请求归配')]
    function postAssignRole()
    {
        if ($this->session->getLoginUserID() === $this->request->getGet('user_id')) {
            $this->getMessageManager()->addWarning(__('不能给自己分配权限！'));
            $this->redirect('*/backend/user/listing');
        }
        /**@var UserRole $userRole */
        $userRole = ObjectManager::getInstance(UserRole::class);
        try {
            $userRole->clearData()->setData($this->request->getPost())->save(true);
            $this->getMessageManager()->addSuccess(__('角色分配成功！'));
        } catch (\Exception $exception) {
            $this->getMessageManager()->addWarning(__('角色分配失败！'));
            if(DEV)$this->getMessageManager()->addException($exception);
        }
        $this->redirect('*/backend/user/assignRole');
    }
}