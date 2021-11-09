<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Admin\Helper;

class Data extends \Weline\Framework\App\Helper
{
    function __construct()
    {
    }

    function drawImage()
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
        for ($i = 0; $i < 4; $i++) {
            # --5-1 设置字体大小
            $fontsize = 6;
            # --5-2 设置字体颜色，随机颜色
            $fontcolor = imagecolorallocate($image, rand(0, 120), rand(0, 120), rand(0, 120));      //0-120深颜色
            # --5-3 设置数字
            $fontcontent = rand(0, 9);
            # --5-4 .=连续定义变量
            $captcha_code .= $fontcontent;
            # --5-5 设置坐标
            $x = ($i * 100 / 4) + rand(5, 10);
            $y = rand(5, 10);
            imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);
        }
    }
}