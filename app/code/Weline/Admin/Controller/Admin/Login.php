<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Controller\Admin;

use Weline\Admin\Helper\Data;
use Weline\Admin\Model\AdminUser;
use Weline\Framework\App\Session\BackendSession;
use Weline\Framework\Manager\ObjectManager;
use Weline\Framework\Session\SessionInterface;
use Weline\Framework\System\Security\Encrypt;

class Login extends \Weline\Framework\App\Controller\BackendController
{
    protected AdminUser $adminUser;
    protected BackendSession|SessionInterface $session;
    private Data $helper;

    function __construct(
        AdminUser $adminUser,
        Data      $helper
    )
    {
        $this->adminUser = $adminUser;
        $this->session = $this->getSession();
        $this->helper = $helper;
    }

    function post()
    {
        if ($this->_request->isPost()) {
            # 验证 form 表单
            if (empty($this->getSession()->getData('form_key'))) {
                $this->noRouter();
            }
            $adminUsernameUser = $this->helper->getRequestAdminUser();
            if (!$adminUsernameUser->getId()) {
                $this->redirect($this->getUrl('?error=' . __('账户不存在！')));
            }
            if ($adminUsernameUser->getAttemptTimes() > 6) {
                $this->redirect($this->getUrl('?error=' . __('你的账户因尝试多次登录，已被锁定！请联系其他管理员开通。')));
            }
            # 自增尝试登录次数
            try {
                $adminUsernameUser->addAttemptTimes()->save();
            } catch (\Exception $exception) {
                $this->redirect($this->getUrl('?error=' . __('登录异常！')));
            }
            # 如果大于2次的尝试登录 验证客户提供的验证码
            if ($adminUsernameUser->getAttemptTimes() > 2) {
                $this->session->setData('need_backend_verification_code', 1);
            }
            # 尝试登录
            $password = trim($this->_request->getParam('password'));
            if (password_verify($password, $adminUsernameUser->getPassword())) {
                $adminUsernameUser->unsetData('password');
                $this->_session->login($adminUsernameUser->getData());
                # 重置 尝试登录次数
                $adminUsernameUser->resetAttemptTimes();
                # 跳转首页
                $this->redirect($this->getUrl());
            } else {
                $msg = __('登录凭据错误！');
                $this->getSession()->setData('backend_login_error', $msg);
                $this->redirect($this->getUrl());
            }
        } else {
            # get请求404
            $this->_request->getResponse()->noRouter();
        }
    }

    /**
     * @DESC          # 获取验证码
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/11/9 23:54
     * 参数区：
     * @return bool
     */
    function verificationCode(): bool
    {
        # --1 设置验证码图片的大小
        $image = imagecreatetruecolor(100, 30);
        # --2 设置验证码颜色 imagecolorallocate(int im, int red, int green, int blue);
        $bgcolor = imagecolorallocate($image, 255, 255, 255); //#ffffff
        # --3 区域填充 int imagefill(int im, int x, int y, int col) (x,y) 所在的区域着色,col 表示欲涂上的颜色
        imagefill($image, 0, 0, $bgcolor);
        # --4 设置变量
        $captcha_code = "";
        # --5 生成随机数字
        for ($i = 0; $i < 6; $i++) {
            # --5-1 设置字体大小
            $fontsize = 6;
            # --5-2 设置字体颜色，随机颜色
            $fontcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));      //0-120深颜色
            # --5-3 设置数字
            $fontcontent = rand(0, 9);
            # --5-4 .=连续定义变量
            $captcha_code .= $fontcontent;
            # --5-5 设置坐标
            $x = intval(($i * 100 / 6) + rand(5, 10));
            $y = rand(5, 10);
            imagestring($image, $fontsize, $x, $y, (string)$fontcontent, $fontcolor);
        }
        $this->session->setData('verification_code', $captcha_code);

        # --6 增加干扰元素，设置雪花点
        for ($i = 0; $i < 200; $i++) {
            # --6-1 设置点的颜色，50-200颜色比数字浅，不干扰阅读
            $pointcolor = imagecolorallocate($image, rand(50, 200), rand(50, 200), rand(50, 200));
            # --6-2 imagesetpixel — 画一个单一像素
            imagesetpixel($image, rand(1, 99), rand(1, 29), $pointcolor);
        }
        # --7 增加干扰元素，设置横线
        for ($i = 0; $i < 4; $i++) {
            # --7-1 设置线的颜色
            $linecolor = imagecolorallocate($image, rand(80, 220), rand(80, 220), rand(80, 220));
            # --7-2 设置线，两点一线
            imageline($image, rand(1, 99), rand(1, 29), rand(1, 99), rand(1, 29), $linecolor);
        }

        # --8 >设置头部，image/png
        header('Content-Type: image/png');
        # --9 >imagepng() 建立png图形函数
        imagepng($image);
        # --10 >imagedestroy() 结束图形函数 销毁$image
        imagedestroy($image);
        return $image;
    }
}