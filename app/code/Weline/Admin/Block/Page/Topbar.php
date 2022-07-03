<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Block\Page;

use Weline\Backend\Model\BackendUser;
use Weline\Admin\Session\AdminSession;
use Weline\Backend\Model\Config;
use Weline\Framework\Database\AbstractModel;
use Weline\Framework\View\Data\DataInterface;
use Weline\Framework\View\Template;

class Topbar extends \Weline\Framework\View\Block
{
    public string $_template = 'Weline_Admin::templates/common/page/topbar.phtml';
    private Config $config;
    private AdminSession $session;
    private ?BackendUser $user = null;

    public function __construct(Config $config, AdminSession $session, array $data=[])
    {
        $this->config  = $config;
        $this->session = $session;
        parent::__construct($data);
        $this->getUser();
    }

    public function getAvatar()
    {
        /**@var BackendUser $user */
        $user   = $this->getUser();
        $avatar = $user->getAvatar();
        if (empty($avatar)) {
            if ($avatar = $this->config->getConfig('admin_default_avatar', 'Weline_Admin')) {
                $avatar = Template::getInstance()->fetchTagSourceFile(DataInterface::view_STATICS_DIR, $avatar);
            }
        }
        return $avatar;
    }

    public function getUser(): BackendUser|AbstractModel
    {
        if (empty($this->user)) {
            $this->user = $this->session->getLoginUser();
        }
        return $this->user;
    }
}
