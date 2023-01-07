<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 作者：Admin
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 * 日期：2023/1/4 23:51:24
 */

namespace Weline\Acl\Model;

use Weline\Framework\Database\Api\Db\Ddl\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Acl extends \Weline\Framework\Database\Model
{
    public const fields_ID            = 'acl_id';
    public const fields_ACL_ID        = 'acl_id';
    public const fields_SOURCE_NAME   = 'source_name';
    public const fields_DOCUMENT      = 'document';
    public const fields_PARENT_SOURCE = 'parent_source';
    public const fields_ROUTER        = 'router';
    public const fields_ROUTE         = 'route';
    public const fields_METHOD        = 'method';
    public const fields_REWRITE       = 'rewrite';
    public const fields_MODULE        = 'module';
    public const fields_CLASS         = 'class';
    public const fields_TYPE          = 'type';

    public function setAclId(string $acl_id): static
    {
        return $this->setData(self::fields_ACL_ID, $acl_id);
    }

    public function setSourceName(string $source_name): static
    {
        return $this->setData(self::fields_SOURCE_NAME, $source_name);
    }

    public function setDocument(string $document): static
    {
        return $this->setData(self::fields_DOCUMENT, $document);
    }

    public function setParentSource(string $parent_source): static
    {
        return $this->setData(self::fields_PARENT_SOURCE, $parent_source);
    }

    public function setRouter(string $router): static
    {
        return $this->setData(self::fields_ROUTER, $router);
    }

    public function setRoute(string $route): static
    {
        return $this->setData(self::fields_ROUTE, $route);
    }

    public function setMethod(string $method): static
    {
        return $this->setData(self::fields_METHOD, $method);
    }

    public function setRewrite(string $rewrite): static
    {
        return $this->setData(self::fields_REWRITE, $rewrite);
    }

    public function setModule(string $module): static
    {
        return $this->setData(self::fields_MODULE, $module);
    }

    public function setClass(string $class): static
    {
        return $this->setData(self::fields_CLASS, $class);
    }

    public function setType(string $type): static
    {
        return $this->setData(self::fields_TYPE, $type);
    }

    public function getAclId(): string
    {
        return $this->getData(self::fields_ACL_ID);
    }

    public function getSourceName(): string
    {
        return $this->getData(self::fields_SOURCE_NAME);
    }

    public function getDocument(): string
    {
        return $this->getData(self::fields_DOCUMENT);
    }

    public function getParentSource(): string
    {
        return $this->getData(self::fields_PARENT_SOURCE);
    }

    public function getRouter(): string
    {
        return $this->getData(self::fields_ROUTER);
    }

    public function getRoute(): string
    {
        return $this->getData(self::fields_ROUTE);
    }

    public function getMethod(): string
    {
        return $this->getData(self::fields_METHOD);
    }

    public function getRewrite(): string
    {
        return $this->getData(self::fields_REWRITE);
    }

    public function getModule(): string
    {
        return $this->getData(self::fields_MODULE);
    }

    public function getClass(): string
    {
        return $this->getData(self::fields_CLASS);
    }

    public function getType(): string
    {
        return $this->getData(self::fields_TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setup(ModelSetup $setup, Context $context): void
    {
        $this->install($setup, $context);
    }

    /**
     * @inheritDoc
     */
    public function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    public function install(ModelSetup $setup, Context $context): void
    {
//        $setup->dropTable();
        if (!$setup->tableExist()) {
            $setup->createTable()
                  ->addColumn(
                      self::fields_ID,
                      TableInterface::column_type_INTEGER,
                      null, 'primary key auto_increment', 'ACL权限ID'
                  )
                  ->addColumn(
                      self::fields_SOURCE_NAME,
                      TableInterface::column_type_VARCHAR,
                      255, 'not null unique', 'ACL资源名称'
                  )
                  ->addColumn(
                      self::fields_DOCUMENT,
                      TableInterface::column_type_TEXT,
                      null, 'not null', 'ACL资源描述'
                  )
                  ->addColumn(
                      self::fields_PARENT_SOURCE,
                      TableInterface::column_type_VARCHAR,
                      255, 'not null', 'ACL父级资源'
                  )
                  ->addColumn(
                      self::fields_ROUTER,
                      TableInterface::column_type_VARCHAR,
                      255, 'not null', 'ACL路由前缀'
                  )
                  ->addColumn(
                      self::fields_REWRITE,
                      TableInterface::column_type_VARCHAR,
                      255, 'default ""', 'ACL路由重写'
                  )
                  ->addColumn(
                      self::fields_ROUTE,
                      TableInterface::column_type_VARCHAR,
                      255, '', 'ACL路由'
                  )
                  ->addColumn(
                      self::fields_METHOD,
                      TableInterface::column_type_VARCHAR,
                      6, 'default ""', 'ACL路由请求方法'
                  )
                  ->addColumn(
                      self::fields_MODULE,
                      TableInterface::column_type_VARCHAR,
                      255, 'not null', 'ACL模组'
                  )
                  ->addColumn(
                      self::fields_CLASS,
                      TableInterface::column_type_VARCHAR,
                      255, 'not null', '控制器类'
                  )
                  ->addColumn(
                      self::fields_TYPE,
                      TableInterface::column_type_VARCHAR,
                      12, 'not null', '类型'
                  )
                  ->create();
        }
    }
}