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
    private string $media_dir = PUB . 'media' . DS;
    private string $uploader_dir = 'uploader' . DS;
    private string $module_dir = '';
    private string $module_name = '';
    private array $accepted_origins = ['http://localhost', 'http://192.168.1.1', 'http://127.0.0.1'];
    private array $ext = ['gif', 'jpg', 'png'];


    public function __construct()
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            // 验证来源是否在白名单内
            if (in_array($_SERVER['HTTP_ORIGIN'], $this->accepted_origins)) {
                header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
            } else {
                header('HTTP/1.1 403 Origin Denied');
                exit;
            }
        }
    }

    /**
     * @DESC          # 添加域名白名单
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/5/3 22:26
     * 参数区：
     *
     * @param array $origin_domains
     *
     * @return $this
     */
    public function addAcceptOriginDomain(array $origin_domains): static
    {
        $this->ext = array_merge($this->ext, $origin_domains);
        return $this;
    }

    /**
     * @DESC          # 添加拓展名
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/5/3 22:22
     * 参数区：
     *
     * @param array $extends
     *
     * @return $this
     */
    public function addExt(array $extends): static
    {
        $this->ext = array_merge($this->ext, $extends);
        return $this;
    }

    /**
     * @DESC          # 检测文件名
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2022/5/3 22:20
     * 参数区：
     *
     * @param string $filename
     */
    public function checkFilename(string $filename)
    {
        // 简单的过滤一下文件名是否合格
        if (preg_match('/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/u', $filename)) {
            header('HTTP/1.1 400 Invalid file name.');
            exit;
        }

        // 验证扩展名
        if (!in_array(strtolower(pathinfo($filename, PATHINFO_EXTENSION)), $this->ext)) {
            header('HTTP/1.1 400 Invalid extension.');
            exit;
        }
    }

    /**
     * @DESC          # 处理文件
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/10/12 13:10
     * 参数区：
     *
     * @param string $module_name 需要设置上传的目录位置
     * @param string $base_dir
     * @param string $module_dir
     *
     * @return array
     * @throws Exception
     */
    public function saveFiles(string $module_name = '', string $module_dir = '', string $base_dir = ''): array
    {
        if ($module_dir) {
            $this->setModuleDir($module_dir);
        }
        if ($base_dir) {
            $this->setBaseUploaderDir($base_dir);
        }
        if ($module_name) {
            $this->setModuleName(str_replace('_', DS, $module_name));
        }
        $result = [];
        if (isset($_FILES['file']['tmp_name'])) {
            $filename = $_FILES['file']['name'];
            $result[] = $this->saveFile($_FILES['file']['tmp_name'], $filename);
        } else {
            foreach ($_FILES as $FILE) {
                $filename = $FILE['tmp_name'];
                $result[] = $this->saveFile($FILE['tmp_name'], $filename);
            }
        }
        return $result;
    }

    /**
     * @DESC          # 保存文件
     *
     * @AUTH    秋枫雁飞
     * @EMAIL aiweline@qq.com
     * @DateTime: 2021/10/12 13:10
     * 参数区：
     *
     * @param string $tmp_file
     * @param string $filename 存储位置
     *
     * @return string
     * @throws Exception
     */
    public function saveFile(string $tmp_file, string $filename): string
    {
        if (str_starts_with($filename, BP)) {
            $this->checkFilename($filename);
        } else {
            $this->checkFilename($filename);
            $filename = $this->getBaseUploaderDir() . $filename;
        }
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }
        if (move_uploaded_file($tmp_file, $filename)) {
            $filename = str_replace(BP, '', $filename);
            return str_replace('\\', '/', $filename);
        } else {
            throw new Exception(__('文件上传失败:%1 ', $filename));
        }
    }

    /**
     * @return string
     */
    #[Pure] public function getBaseUploaderDir(): string
    {
        return $this->media_dir . rtrim($this->uploader_dir, DS) . DS . $this->getModuleName() . DS . $this->getModuleDir() . DS;
    }

    /**
     * @param string $uploader_dir
     *
     * @return Uploader
     */
    public function setBaseUploaderDir(string $uploader_dir): Uploader
    {
        $this->uploader_dir = rtrim($uploader_dir, DS) . DS;
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

    /**
     * @return string
     */
    public function getModuleDir(): string
    {
        return $this->module_dir;
    }

    /**
     * @param string $module_dir
     */
    public function setModuleDir(string $module_dir): void
    {
        $this->module_dir = $module_dir;
    }
}
