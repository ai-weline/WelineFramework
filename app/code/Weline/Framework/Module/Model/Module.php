<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\Framework\Module\Model;

use Weline\Framework\DataObject\DataObject;

class Module extends DataObject
{
    private string $name;
    private bool $status;
    private string $version;
    private string $router;
    private string $description;
    private string $base_path;
    private string $namespace_path;
    private string $path;

    const name = 'name';
    const status = 'status';
    const version = 'version';
    const router = 'router';
    const description = 'description';
    const base_path = 'base_path';
    const namespace_path = 'namespace_path';
    const path = 'path';

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus(bool $status): static
    {
        $this->setData(self::status, $status);
        $this->status = $status;
        return $this;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): static
    {
        $this->setData(self::version, $version);
        $this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getRouter(): string
    {
        return $this->router;
    }

    /**
     * @param string $router
     */
    public function setRouter(string $router): static
    {
        $this->setData(self::router, $router);
        $this->router = $router;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): static
    {
        $this->setData(self::description, $description);
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->base_path;
    }

    /**
     * @param string $base_path
     */
    public function setBasePath(string $base_path): static
    {
        $this->setData(self::base_path, $base_path);
        $this->base_path = $base_path;
        return $this;
    }


    /**
     * @return string
     */
    public function getNamespacePath(): string
    {
        return $this->namespace_path;
    }

    /**
     * @param string $namespace_path
     */
    public function setNamespacePath(string $namespace_path): static
    {
        $this->setData(self::namespace_path, $namespace_path);
        $this->namespace_path = $namespace_path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath(string $path): static
    {
        $this->setData(self::path, $path);
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): static
    {
        $this->setData(self::name, $name);
        $this->name = $name;
        return $this;
    }

    function getModuleFile(string $filename): string
    {
        return BP . $this->getBasePath() . $filename;
    }
}