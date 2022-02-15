<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\System\File;

use Weline\Framework\App\Env;

class Uploader
{
    private string $base_uploader_dir;

    function __construct(string $basepath, $module_name)
    {
        $this->base_uploader_dir = rtrim($basepath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR.$module_name.DIRECTORY_SEPARATOR;
        $this->initFiles();
    }



    /**
     * @DESC          # 处理文件
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/10/12 13:10
     * 参数区：
     * @return array
     */
    function initFiles(): array
    {
        $result = [];
        if (isset($_FILES['file']['tmp_name'])) {
            $result[] = $this->saveFile($_FILES['file']['tmp_name'], $this->base_uploader_dir . $_FILES['file']['name']);
        } else {
            foreach ($_FILES as $FILE) {
                $result[] = $this->saveFile($FILE['file']['tmp_name'], $this->base_uploader_dir . $FILE['file']['name']);
            }
        }
        return $result;
    }

    /**
     * @DESC          # 保存文件
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/10/12 13:10
     * 参数区：
     * @param string $file_tmp_name 文件tmp名
     * @param string $filepath 存储位置
     * @return string
     */
    function saveFile(string $file_tmp_name, string $filepath): string
    {
        $dir  = dirname($filepath);
        if (!is_dir( $dir )) {
            mkdir( $dir ,765,true);
        }
        move_uploaded_file($file_tmp_name, $filepath);
        return str_replace(BP, '', $filepath);
    }

    /**
     * @return string
     */
    public function getBaseUploaderDir(): string
    {
        return $this->base_uploader_dir;
    }

    /**
     * @param string $base_uploader_dir
     * @return Uploader
     */
    public function setBaseUploaderDir(string $base_uploader_dir): Uploader
    {
        $this->base_uploader_dir = $base_uploader_dir;
        return $this;
    }
}