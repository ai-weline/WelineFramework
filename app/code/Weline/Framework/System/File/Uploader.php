<?php

declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\System\File;

use JetBrains\PhpStorm\Pure;
use Weline\Framework\App\Env;
use Weline\Framework\App\Exception;

class Uploader
{
    private string $base_uploader_dir = PUB . 'media' . DIRECTORY_SEPARATOR . 'uploader' . DIRECTORY_SEPARATOR;
    private string $module_name = '';


    /**
     * @DESC          # 处理文件
     *
     * @AUTH  秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/10/12 13:10
     * 参数区：
     * @param string $module_name 需要设置上传的目录位置
     * @return array
     */
    public function saveFiles(string $module_name = '', string $move_to_dir = ''): array
    {
        if ($move_to_dir) {
            $this->setBaseUploaderDir($move_to_dir);
        }
        if ($module_name) {
            $this->setModuleName($module_name);
        }
        $result = [];
        if (isset($_FILES['file']['tmp_name'])) {
            $result[] = $this->saveFile($_FILES['file']['tmp_name'], $this->getBaseUploaderDir() . $_FILES['file']['name']);
        } else {
            foreach ($_FILES as $FILE) {
                $result[] = $this->saveFile($FILE['file']['tmp_name'], $this->getBaseUploaderDir() . $FILE['file']['name']);
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
     * @param string $file_tmp 文件tmp名
     * @param string $filename 存储位置
     * @return string
     * @throws Exception
     */
    public function saveFile(string $file_tmp, string $filename): string
    {
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
        if (move_uploaded_file($file_tmp, $filename)) {
            return str_replace(BP, '', $filename);
        } else {
            throw new Exception(__('文件上传失败:%1 ', $filename));
        }
    }

    /**
     * @return string
     */
    #[Pure] public function getBaseUploaderDir(): string
    {
        return rtrim($this->base_uploader_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $this->getModuleName() . DIRECTORY_SEPARATOR;
    }

    /**
     * @param string $base_uploader_dir
     * @return Uploader
     */
    public function setBaseUploaderDir(string $base_uploader_dir): Uploader
    {
        $this->base_uploader_dir = rtrim($base_uploader_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        return $this;
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->module_name;
    }

    /**
     * @param string $module_name 模块名
     */
    public function setModuleName(string $module_name): Uploader
    {
        $this->module_name = $module_name;
        return $this;
    }
}
