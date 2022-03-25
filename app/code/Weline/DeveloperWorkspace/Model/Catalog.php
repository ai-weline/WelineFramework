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

class Catalog extends \Weline\Framework\Database\Model
{
//    const table       = 'dev_document_catalog';
    public const fields_ID   = 'id';
    public const fields_NAME = 'name';
    public const fields_PID  = 'pid';

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
        # 更新提示
        $setup->getPrinting()->setup($context->getVersion());
    }

    /**
     * @inheritDoc
     */
    public function install(ModelSetup $setup, Context $context): void
    {
        $setup->getPrinting()->setup('安装数据表...', $setup->getTable());
        if (!$setup->tableExist()) {
            $setup->createTable('目录')
                  ->addColumn('id', TableInterface::column_type_INTEGER, 0, 'primary key auto_increment', 'ID')
                  ->addColumn('name', TableInterface::column_type_VARCHAR, 60, 'not null ', '目录名')
                  ->addColumn('pid', TableInterface::column_type_INTEGER, 0, '', '父目录')
                  ->create();
        } else {
            $setup->getPrinting()->warning('跳过安装数据表...', $setup->getTable());
        }
    }

    public function getName()
    {
        return $this->getData(self::fields_NAME);
    }

    public function setName(string $name): Catalog
    {
        return $this->setData(self::fields_NAME, $name);
    }

    public function getPid()
    {
        return $this->getData(self::fields_PID);
    }

    public function setPid(string|int $pid): Catalog
    {
        return $this->setData(self::fields_PID, $pid);
    }
}
