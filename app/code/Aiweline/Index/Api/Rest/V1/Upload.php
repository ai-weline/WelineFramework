<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Aiweline\Index\Api\Rest\V1;

class Upload extends \Weline\Framework\App\Controller\FrontendRestController
{
    function postIndex(){
        $filename = $_FILES['file']['name'];
        $filetmp = $_FILES['file']['tmp_name'];
        $dir_name = __DIR__.DIRECTORY_SEPARATOR;
        $file_path = $dir_name.$filename;
        if(!is_dir(__DIR__.DIRECTORY_SEPARATOR)){
            mkdir($dir_name);
        }
        move_uploaded_file($filetmp, $file_path);
        return $file_path;
    }
}