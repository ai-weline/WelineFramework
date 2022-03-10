<?php
declare(strict_types=1);

/*
 * 本文件由 秋枫雁飞 编写，所有解释权归Aiweline所有。
 * 邮箱：aiweline@qq.com
 * 网址：aiweline.com
 * 论坛：https://bbs.aiweline.com
 */

namespace Weline\DeveloperWorkspace\Model;

use Weline\Framework\Database\Api\Db\TableInterface;
use Weline\Framework\Setup\Data\Context;
use Weline\Framework\Setup\Db\ModelSetup;

class Document extends \Weline\Framework\Database\Model
{
    const fields_ID = 'id';
    const fields_TITLE = 'title';
    const fields_AUTHOR_ID = 'author_id';
    const fields_TAG_ID = 'tag_id';
    const fields_CONTEND = 'content';

    /**
     * @inheritDoc
     */
    function setup(ModelSetup $setup, Context $context): void
    {
        /*$setup->getPrinting()->setup('安装数据表...');
        $setup->createTable('开发文章')
            ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'primary key auto_increment ', 'ID')
            ->addColumn(self::fields_TITLE, TableInterface::column_type_VARCHAR, 120, 'not null', '标题')
            ->addColumn(self::fields_AUTHOR_ID, TableInterface::column_type_INTEGER, 0, 'default 0', '作者ID')
            ->addColumn(self::fields_TAG_ID, TableInterface::column_type_INTEGER, 0, 'default 0', '标签ID')
            ->addColumn(self::fields_CONTEND, TableInterface::column_type_TEXT, 0, 'not null', '内容')
            ->create();*/
    }

    /**
     * @inheritDoc
     */
    function upgrade(ModelSetup $setup, Context $context): void
    {
        // TODO: Implement upgrade() method.
    }

    /**
     * @inheritDoc
     */
    function install(ModelSetup $setup, Context $context): void
    {
        $setup->getPrinting()->setup('安装数据表...',$setup->getTable());
        if(!$setup->tableExist()){
            $setup->createTable('开发文章')
                ->addColumn(self::fields_ID, TableInterface::column_type_INTEGER, 0, 'primary key auto_increment ', 'ID')
                ->addColumn(self::fields_TITLE, TableInterface::column_type_VARCHAR, 120, 'not null', '标题')
                ->addColumn(self::fields_AUTHOR_ID, TableInterface::column_type_INTEGER, 0, 'default 0', '作者ID')
                ->addColumn(self::fields_TAG_ID, TableInterface::column_type_INTEGER, 0, 'default 0', '标签ID')
                ->addColumn(self::fields_CONTEND, TableInterface::column_type_TEXT, 0, 'not null', '内容')
                ->create();
        }else{
            $setup->getPrinting()->setup('跳过安装数据表...',$setup->getTable());
        }
    }

    function getTitle()
    {
        return $this->getData(self::fields_TITLE);
    }

    function setTitle(string $title): Document
    {
        return $this->setData(self::fields_TITLE, $title);
    }

    function getAuthorId()
    {
        return $this->getData(self::fields_AUTHOR_ID);
    }

    function setAuthorID(string|int $author_id): Document
    {
        return $this->setData(self::fields_AUTHOR_ID, $author_id);
    }

    function getTagId()
    {
        return $this->getData(self::fields_TAG_ID);
    }

    function setTagID(string|int $tag_id): Document
    {
        return $this->setData(self::fields_TAG_ID, $tag_id);
    }

    function getContent()
    {
        return $this->getData(self::fields_CONTEND);
    }

    function setContent(string $content): Document
    {
        return $this->setData(self::fields_CONTEND, $content);
    }
}