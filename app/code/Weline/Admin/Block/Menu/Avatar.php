<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Block\Menu;

use Weline\Admin\Model\AdminUser;
use Weline\Backend\Model\Config;
use Weline\Framework\App\Session\BackendSession;

class Avatar extends \Weline\Framework\View\Block
{
    protected $_template = 'Weline_Admin::templates/theme/material/menu/avatar.phtml';
    private $config;
    private $session;

    function __construct(Config $config, BackendSession $session)
    {
        $this->config = $config;
        $this->session = $session;
        # FIXME 切换后台主题
        parent::__construct();
    }

    function getAvatar()
    {
        /**@var AdminUser $user*/
        $user = $this->session->getLoginUser();
        $avatar = '';
        if($user){
            $avatar = $user->getAvatar();
        }
        if(empty($avatar)){
            $avatar = $this->config->getConfig('admin_default_avatar', 'Aiweline_Admin');
        }
        return $avatar;
    }
}